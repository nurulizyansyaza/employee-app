#!/usr/bin/env bash
# Employee App — Production Server Setup (Self-Hosted GitHub Actions Runner)
#
# Run this ONCE on a fresh Ubuntu 22.04/24.04 server as root (or via sudo).
# Architecture: no SSH deploy, no inbound ports — GitHub runner pulls jobs out.
#
# Usage:
#   sudo bash server-setup.sh
#
set -euo pipefail

DEPLOY_DIR="/data/employee-app"
RUNNER_USER="github-runner"
RUNNER_DIR="/home/${RUNNER_USER}/actions-runner"
RUNNER_VERSION="2.319.1"
RUNNER_LABELS="self-hosted,Linux,employee-app"
REPO_URL="https://github.com/nurulizyansyaza/employee-app"
DOMAIN="employee.nurulizyansyaza.com"

if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: must be run as root (use sudo)." >&2
    exit 1
fi

echo "=========================================="
echo " Employee App — Server Setup"
echo " Deploy dir : ${DEPLOY_DIR}"
echo " Runner user: ${RUNNER_USER}"
echo "=========================================="

###############################################################################
# 1. System packages
###############################################################################
echo "[1/7] Updating system & installing prerequisites..."
apt-get update -qq
apt-get upgrade -y -qq
apt-get install -y -qq ca-certificates curl gnupg lsb-release ufw jq tar

###############################################################################
# 2. Docker
###############################################################################
echo "[2/7] Installing Docker..."
if ! command -v docker >/dev/null 2>&1; then
    install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
        | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
    chmod a+r /etc/apt/keyrings/docker.gpg

    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" \
        > /etc/apt/sources.list.d/docker.list

    apt-get update -qq
    apt-get install -y -qq docker-ce docker-ce-cli containerd.io \
        docker-buildx-plugin docker-compose-plugin
    systemctl enable --now docker
else
    echo "  Docker already installed."
fi

###############################################################################
# 3. Host nginx (reverse proxy — routes employee.nurulizyansyaza.com → Docker)
###############################################################################
echo "[3/8] Installing & configuring host nginx..."
if ! command -v nginx >/dev/null 2>&1; then
    apt-get install -y -qq nginx
    systemctl enable nginx
fi

VHOST_FILE="/etc/nginx/sites-available/${DOMAIN}"
cat > "${VHOST_FILE}" << VHOSTEOF
server {
    listen 80;
    server_name ${DOMAIN};

    # Trust Cloudflare proxy IPs for real IP forwarding
    set_real_ip_from 172.16.0.0/12;
    set_real_ip_from 10.0.0.0/8;
    real_ip_header CF-Connecting-IP;

    location / {
        proxy_pass         http://127.0.0.1:8082;
        proxy_set_header   Host \$host;
        proxy_set_header   X-Real-IP \$remote_addr;
        proxy_set_header   X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto https;
        proxy_read_timeout 300;
        proxy_connect_timeout 300;
        proxy_send_timeout 300;
        client_max_body_size 50M;
    }
}
VHOSTEOF

ln -sf "${VHOST_FILE}" /etc/nginx/sites-enabled/
# Remove default catch-all so unknown hosts don't fall through to wrong app
rm -f /etc/nginx/sites-enabled/default

nginx -t && systemctl reload nginx

###############################################################################
# 4. Firewall (allow SSH + HTTP; Cloudflare Proxy handles HTTPS)
###############################################################################
echo "[4/8] Configuring UFW..."
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 80/tcp
ufw --force enable

###############################################################################
# 4. Runner user (+ docker group)
###############################################################################
echo "[5/8] Creating runner user: ${RUNNER_USER}..."
if ! id -u "${RUNNER_USER}" >/dev/null 2>&1; then
    useradd -m -s /bin/bash -G docker "${RUNNER_USER}"
else
    usermod -aG docker "${RUNNER_USER}"
fi

###############################################################################
# 5. Deploy directory + docker-compose.prod.yml + .env.production
###############################################################################
echo "[6/8] Preparing ${DEPLOY_DIR}..."
mkdir -p "${DEPLOY_DIR}"

# Fetch docker-compose.prod.yml from the repo (public-readable raw URL).
# If your repo is private, replace this with a `git clone` using a deploy key.
echo "  Fetching docker-compose.prod.yml from ${REPO_URL}..."
curl -fsSL -o "${DEPLOY_DIR}/docker-compose.prod.yml" \
    "https://raw.githubusercontent.com/nurulizyansyaza/employee-app/main/docker-compose.prod.yml" \
    || echo "  WARN: failed to fetch — copy it manually to ${DEPLOY_DIR}/docker-compose.prod.yml"

if [ ! -f "${DEPLOY_DIR}/.env.production" ]; then
    cat > "${DEPLOY_DIR}/.env.production" <<'ENVEOF'
APP_NAME="Employee App"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://employee.nurulizyansyaza.com
APP_KEY=CHANGE_THIS_APP_KEY

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=employee_app
DB_USERNAME=employee
DB_PASSWORD=CHANGE_THIS_PASSWORD

CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database

MAIL_MAILER=log

ENVEOF
    chmod 600 "${DEPLOY_DIR}/.env.production"
    echo "  Created ${DEPLOY_DIR}/.env.production (edit it before first deploy!)"
else
    echo "  ${DEPLOY_DIR}/.env.production already exists — not overwriting."
fi

SUDOERS_FILE="/etc/sudoers.d/90-github-runner-deploy"
if [[ ! -f "${SUDOERS_FILE}" ]]; then
    printf '%s ALL=(root) NOPASSWD: /usr/bin/chown\n' "${RUNNER_USER}" > "${SUDOERS_FILE}"
    chmod 440 "${SUDOERS_FILE}"
    echo "  Sudoers rule written to ${SUDOERS_FILE}"
fi

chown -R "${RUNNER_USER}:${RUNNER_USER}" "${DEPLOY_DIR}"

###############################################################################
# 6. GitHub Actions self-hosted runner
###############################################################################
echo "[7/8] Installing GitHub Actions runner v${RUNNER_VERSION}..."
if [ -d "${RUNNER_DIR}" ] && [ -f "${RUNNER_DIR}/.runner" ]; then
    echo "  Runner already configured at ${RUNNER_DIR} — skipping download/config."
else
    sudo -u "${RUNNER_USER}" mkdir -p "${RUNNER_DIR}"
    cd "${RUNNER_DIR}"

    ARCH=$(uname -m)
    case "${ARCH}" in
        x86_64)  RUNNER_ARCH="x64" ;;
        aarch64) RUNNER_ARCH="arm64" ;;
        *) echo "ERROR: unsupported arch ${ARCH}" >&2; exit 1 ;;
    esac

    TARBALL="actions-runner-linux-${RUNNER_ARCH}-${RUNNER_VERSION}.tar.gz"
    sudo -u "${RUNNER_USER}" curl -fsSL -o "${TARBALL}" \
        "https://github.com/actions/runner/releases/download/v${RUNNER_VERSION}/${TARBALL}"
    sudo -u "${RUNNER_USER}" tar xzf "${TARBALL}"
    rm -f "${TARBALL}"

    echo ""
    echo "  >>> ACTION REQUIRED <<<"
    echo "  1. Open: ${REPO_URL}/settings/actions/runners/new"
    echo "  2. Copy the registration token shown."
    echo "  3. Then run, as ${RUNNER_USER}:"
    echo ""
    echo "     sudo -u ${RUNNER_USER} bash -c 'cd ${RUNNER_DIR} && ./config.sh \\"
    echo "       --url ${REPO_URL} \\"
    echo "       --token <PASTE_TOKEN_HERE> \\"
    echo "       --name \$(hostname) \\"
    echo "       --labels ${RUNNER_LABELS} \\"
    echo "       --unattended --replace'"
    echo ""
    echo "  4. Then install + start the systemd service (as root):"
    echo ""
    echo "     cd ${RUNNER_DIR}"
    echo "     ./svc.sh install ${RUNNER_USER}"
    echo "     ./svc.sh start"
    echo "     ./svc.sh status"
    echo ""
fi

###############################################################################
# 7. Final reminders
###############################################################################
echo "[8/8] Done with automated steps."
echo ""
echo "=========================================="
echo " REMAINING MANUAL STEPS"
echo "=========================================="
echo ""
echo " A. Edit ${DEPLOY_DIR}/.env.production and replace:"
echo "      APP_KEY    — generate with:"
echo "        docker run --rm ghcr.io/nurulizyansyaza/employee-app:latest \\"
echo "          php artisan key:generate --show"
echo "      DB_PASSWORD — set a strong password"
echo ""
echo " B. Cloudflare DNS setup (one-time, in Cloudflare dashboard):"
echo "      1. Add A record: employee → <server-public-IP>, proxy enabled (orange cloud)"
echo "      2. SSL/TLS mode: Flexible"
echo ""
echo " C. Register the runner (see commands printed in step [6/7] above)."
echo ""
echo " D. Verify runner is Idle at:"
echo "      ${REPO_URL}/settings/actions/runners"
echo ""
echo " E. Push to main (or run workflow manually) → deploy job runs on this server."
echo ""
echo "=========================================="
