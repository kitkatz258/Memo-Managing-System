import './bootstrap';
import Swal from 'sweetalert2';

window.Swal = Swal;
const theme = localStorage.getItem('theme');
if(theme === 'dark') {
    document.documentElement.classList.add('dark');
}

document.addEventListener('livewire:init', () => {
    Livewire.on('memo-saved', () => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Memo saved successfully',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    });
});

document.addEventListener('DOMContentLoaded',() =>{
    const toggleBtn = document.getElementById('theme-toggle');
    toggleBtn?.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    });
});