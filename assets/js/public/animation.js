document.addEventListener("DOMContentLoaded",(()=>{const t=new IntersectionObserver((t=>{t.forEach((t=>{if(t.isIntersecting){const e=t.target.children;let n=0;[...e].forEach((t=>{t.classList.contains("fade-in")&&(n+=100,t.style.opacity="1",t.style.transition="opacity 1s",t.style.transitionDelay=n+"ms")}))}}))}));[...document.querySelectorAll(".wp-block-columns, main > .wp-block-group")].forEach((e=>{t.observe(e),[...e.children].forEach((t=>{t.classList.contains("fade-in")&&(t.style.opacity="0")}))}))}));