let btn_cancelar = document.getElementsByClassName('ym_btn-remover')[0];
btn_cancelar.style.display = "none";
let btn_save = document.getElementsByClassName('ym_btn-salvar')[0];
btn_save.style.display = "none";


function edit(){
    let btn_edit = document.getElementsByClassName('ym_btn-editar')[0];
    btn_edit.style.display = "none";
    btn_cancelar.style.display = "flex";
    btn_save.style.display = "flex";
    
    let form = document.getElementsByClassName("jp_info-grid")[0]
    const ps = form.querySelectorAll('p');
    ps.forEach(p => {
      p.style.display = 'none';
    })
    
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
      input.style.display = 'block';
    });
}