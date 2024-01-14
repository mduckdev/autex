function wroc(){
    history.back()
}
try{
  document.getElementById("wroc").onclick = wroc;
}catch(e){
  console.log(e);
}

    


try{
  const forms = document.querySelectorAll(".require-additional-confirm");
  forms.forEach(form=>{
    form.onsubmit = function(e) {
      e.preventDefault();
      if (confirm('Czy na pewno chcesz wysłać ten formularz?')) {
        form.submit();
      }
    }
  })
  
}catch(e){
  console.log(e);
}
