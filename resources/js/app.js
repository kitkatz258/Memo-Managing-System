import './bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.default.css';

window.TomSelect = TomSelect;
window.Swal = Swal;

window.toggleAllCategories = function(checked) {
    if(!window.categoryTomSelect) return;
    if(checked) {
        window.categoryTomSelect.setValue(
            Object.keys(window.categoryTomSelect.options)
        );
    } else {
        window.categoryTomSelect.clear();
    }
};

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

document.addEventListener('alpine:init', () => {
    Alpine.store('pdfViewer', {
        open: false,
        url: '',
        downloadUrl: '',
        title: '',
        memoNo: '',
        show(id, memoNo, title) {
            this.url = `/memos/${id}/view`;
            this.downloadUrl = `/memos/${id}/download`;
            this.title = title;
            this.memoNo = memoNo;
            this.open = true;
        },
        close() {
            this.open = false;
            this.url = '';
        }
    });
});