function wroc(){
    history.back()
}
document.getElementById("wroc").onclick = wroc;

    
const form = document.getElementById("returnForm");
form.onsubmit= function(e) {
    e.preventDefault();
    if (confirm('Czy na pewno chcesz wysłać ten formularz?')) {
      form.submit();
    }
  }