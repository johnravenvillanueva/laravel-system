document.addEventListener('DOMContentLoaded', function () {
    animateReportBars();
    initPrintReport();
    initDateFilter();
});

function animateReportBars() {
    document.querySelectorAll('.bar-fill').forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 300);
    });
}

function initPrintReport() {
    const printBtn = document.getElementById('printReport');
    if (printBtn) {
        printBtn.addEventListener('click', function () {
            window.print();
        });
    }
}

function initDateFilter() {
    const form = document.getElementById('reportFilterForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            showToast('Report updated', 'success');
        });
    }
}