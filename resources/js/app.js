import './bootstrap';

import Alpine from 'alpinejs';
import { createApp } from 'vue';
import EmployeeApp from './components/EmployeeApp.vue';

window.Alpine = Alpine;

Alpine.start();

const employeeAppEl = document.getElementById('employee-app');
if (employeeAppEl) {
    createApp(EmployeeApp).mount(employeeAppEl);
}
