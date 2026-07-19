document.querySelectorAll('[data-confirm]').forEach(el=>el.addEventListener('click',e=>{if(!confirm(el.dataset.confirm))e.preventDefault();}));
setTimeout(()=>document.querySelector('.alert')?.remove(),3500);
