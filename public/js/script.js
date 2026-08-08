/*Fichier JS */




const boutonConnect = document.getElementById('monBoutonLogin');
const formulaireLogin = document.getElementById('monFormulaireLogin');

boutonConnect.addEventListener('click',function()
    {
        if(formulaireLogin.style.display==='none'){
            formulaireLogin.style.display ='block';
            boutonConnect.style.display='none';
        }else{
            formulaireLogin.style.display='none';
        }
    }); 



