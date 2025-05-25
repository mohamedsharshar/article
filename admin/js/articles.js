// js/articles.js
// سكريبت مبدئي لإدارة المقالات (إظهار/إخفاء المودال فقط)
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.querySelector('.add-article-btn');
    const modal = document.querySelector('.add-article-modal');
    const closeBtn = document.querySelector('.close-modal');
    if (addBtn && modal && closeBtn) {
        addBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
        });
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        // إغلاق المودال عند الضغط خارج النموذج
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
