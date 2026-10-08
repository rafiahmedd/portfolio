const cursor=document.body.appendChild(Object.assign(document.createElement('div'),{className:'cursor'}));
window.addEventListener('mousemove',e=>{cursor.style.left=e.clientX+'px';cursor.style.top=e.clientY+'px'});
document.addEventListener('mouseover',e=>cursor.classList.toggle('big',!!e.target.closest('a,button,.identity,.work,.portrait')));

const observer=new IntersectionObserver(entries=>{
  entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('show')});
},{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));

const header=document.querySelector('.site-header');
window.addEventListener('scroll',()=>header.classList.toggle('scrolled',window.scrollY>80));
