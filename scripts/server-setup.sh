#!/usr/bin/env bash
set -euo pipefail

DEPLOY_DIR="/opt/employee-app"
APP_USER="deploy"
DOMAIN="employee.nurulizyansyaza.com"
EMAIL="hello@nurulizyansyaza.com"

echo "=========================================="
echo " Employee App — Server Setup"
echo "=========================================="

echo "[1/9] Updating system packages..."
apt-get update -qq && apt-get upgrade -y -qq

echo "[2/9] Installing prerequisites..."
apt-get install -y -qq \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    ufw

echo "[3/9] Installing Docker..."
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg \
    | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
  https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" \
  > /etc/apt/sources.list.d/docker.list

apt-get update -qq
apt-get install -y -qq docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

systemctl enable --now docker

echo "[4/9] Creating deploy user: ${APP_USER}..."
if ! id -u "${APP_USER}" &>/dev/null; then
    useradd -m -s /bin/bash -G docker "${APP_USER}"
    echo "User ${APP_USER} created."
else
    usermod -aG docker "${APP_USER}"
    echo "User ${APP_USER} already exists — added to docker group."
fi

SSH_DIR="/home/${APP_USER}/.ssh"
mkdir -p "${SSH_DIR}"
chmod 700 "${SSH_DIR}"
touch "${SSH_DIR}/authorized_keys"
chmod 600 "${SSH_DIR}/authorized_keys"
chown -R "${APP_USER}:${APP_USER}" "${SSH_DIR}"

echo ""
echo "  !! ACTION REQUIRED: Add your CI/CD SSH public key to:"
echo "     ${SSH_DIR}/authorized_keys"
echo ""

echo "[5/9] Creating deployment directory: ${DEPLOY_DIR}..."
mkdir -p "${DEPLOY_DIR}"
chown "${APP_USER}:${APP_USER}" "${DEPLOY_DIR}"

echo "[6/9] Configuring UFW firewall..."
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw --force enable
echo "UFW configured."

echo "[7/9] Creating production .env template at ${DEPLOY_DIR}/.env.production..."
cat > "${DEPLOY_DIR}/.env.production" << 'ENVEOF'
APP_NAME="Employee App"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://employee.nurulizyansyaza.com

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

APP_KEY=CHANGE_THIS_APP_KEY


CLOUDFLARE_TUNNEL_TOKEN=CHANGE_THIS_TUNNEL_TOKEN


MAIL_MAILER=log

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
ENVEOF

chmod 600 "${DEPLOY_DIR}/.env.production"
chown "${APP_USER}:${APP_USER}" "${DEPLOY_DIR}/.env.production"
echo ""
echo "  !! ACTION REQUIRED: Edit ${DEPLOY_DIR}/.env.production and set:"
echo "     - APP_KEY  (run: php artisan key:generate --show)"
echo "     - DB_PASSWORD"
echo ""

echo "[8/9] Note: Copy docker-compose.prod.yml and docker/ to ${DEPLOY_DIR}/"
echo "     You can do this by cloning your repo or running deploy from CI."

echo "[9/9] Cloudflare Tunnel setup..."
echo ""
echo "  !! MANUAL STEPS — Run these ONCE from your local machine / Cloudflare dashboard:"
echo ""
echo "  1. Go to https://one.dash.cloudflare.com → Networks → Tunnels"
echo "  2. Click 'Create a tunnel' → choose 'Cloudflared' → name it: employee-app"
echo "  3. Copy the tunnel token shown on screen"
echo "  4. On this server, add the token to ${DEPLOY_DIR}/.env.production:"
echo "       CLOUDFLARE_TUNNEL_TOKEN=<paste-token-here>"
echo ""
echo "  5. In the Cloudflare dashboard, configure Public Hostname:"
echo "       Subdomain : employee"
echo "       Domain    : nurulizyansyaza.com"
echo "       Service   : HTTP → nginx:80"
echo ""
echo "  6. Start the stack (cloudflared will connect automatically):"
echo "       cd ${DEPLOY_DIR}"
echo "       docker compose -f docker-compose.prod.yml up -d"
echo ""
echo "  That's it — no port forwarding, no DNS A record, no SSL cert needed."
echo "  Cloudflare automatically creates the CNAME and handles HTTPS."
echo ""

echo "=========================================="
echo " Setup complete!"
echo " Server is ready for deployment."
echo "=========================================="
