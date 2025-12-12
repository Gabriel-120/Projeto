// Basic frontend helpers for the placeholder UI
document.addEventListener('DOMContentLoaded', function(){
    // Set min attribute for date inputs with class 'date-min-today'
    const today = new Date();
    const pad = (n)=> (n<10? '0'+n : n);
    const yyyy = today.getFullYear();
    const mm = pad(today.getMonth()+1);
    const dd = pad(today.getDate());
    const minDate = `${yyyy}-${mm}-${dd}`;
    document.querySelectorAll('input.date-min-today[type="date"]').forEach(function(el){
        if (!el.getAttribute('min')) el.setAttribute('min', minDate);
    });

    // Basic input sanitation: allow only digits on elements with data-digits-only
    document.querySelectorAll('input[data-digits-only]').forEach(function(inp){
        inp.addEventListener('input', function(e){
            this.value = this.value.replace(/\D+/g, '');
        });
    });

    // Luhn check helper for card numbers (simple frontend hint)
    window.luhnValid = function(num){
        const s = num.replace(/\D/g,'');
        let sum = 0, alt = false;
        for (let i = s.length-1; i>=0; i--){
            let n = parseInt(s.charAt(i),10);
            if (alt){ n*=2; if (n>9) n-=9; }
            sum += n; alt = !alt;
        }
        return (sum%10)===0;
    };
});
