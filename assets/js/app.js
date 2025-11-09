document.addEventListener('DOMContentLoaded', ()=>{
  // Daily deals countdown timer
  const timerEl = document.getElementById('dd-timer');
  if(timerEl){
    let seconds = 8*60 + 21;
    setInterval(()=>{
      seconds = Math.max(0, seconds-1);
      const m = String(Math.floor(seconds/60)).padStart(2,'0');
      const s = String(seconds%60).padStart(2,'0');
      timerEl.textContent = `${m}m:${s}s`;
    }, 1000);
  }

  // Banner carousel auto-slider
  const track = document.getElementById('track');
  const dots = document.querySelectorAll('#dots .dot');
  if(track && dots.length > 0){
    let currentIndex = 0;
    const totalSlides = 3;
    
    // Dot click handlers
    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        currentIndex = index;
        updateSlider();
      });
    });
    
    function updateSlider(){
      track.scrollTo({
        left: track.clientWidth * currentIndex,
        behavior: 'smooth'
      });
      dots.forEach((d, i) => {
        d.classList.toggle('active', i === currentIndex);
      });
    }
    
    // Auto-advance every 4 seconds
    setInterval(()=>{
      currentIndex = (currentIndex + 1) % totalSlides;
      updateSlider();
    }, 4000);
  }
});
