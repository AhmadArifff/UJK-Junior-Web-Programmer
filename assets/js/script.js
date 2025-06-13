// Animate new item
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('todoList');
            if (list.children.length) {
                list.children[0].classList.add('highlight');
            }
        });

        // Enter key submits form
        document.getElementById('taskInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.target.form.submit();
            }
        });

        // Simple fade highlight for new/edited items
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('edit')) {
            setTimeout(() => {
                document.querySelector('.todo-item[data-id="'+urlParams.get('edit')+'"]').style.background = '#fff3e0';
            }, 200);
        }