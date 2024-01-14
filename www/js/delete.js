try{
    const forms = document.querySelectorAll(".delete-form");
    forms.forEach(form=>{
      form.onsubmit = function(e) {
        e.preventDefault();
        if (confirm('Przed usunięciem kliencia z bazy oraz powiązanych z nim wypożyczeń upewnij się że spełnione zostały wymogi prawne odnośnie rentencji danych przez określony okres czasu.')) {
          form.submit();
        }
      }
    })
    
  }catch(e){
    console.log(e);
  }
  try{
    const forms = document.querySelectorAll(".edit-form");
    forms.forEach(form=>{
      form.onsubmit = function(e) {
        e.preventDefault();
        if (confirm('Przed usunięciem samochodu z bazy oraz powiązanych z nim wypożyczeń upewnij się że spełnione zostały wymogi prawne odnośnie rentencji danych przez określony okres czasu.')) {
          form.submit();
        }
      }
    })
    
  }catch(e){
    console.log(e);
  }
  
  