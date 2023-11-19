const loadList = document.querySelector("#loadList");
const loadButton = document.querySelector("#loadButton");

loadButton.addEventListener('click', function(){ loadMatches() })

async function loadMatches(){
    let resp = await fetch("load.php");
    let data = await resp.json();

    loadList.innerHTML = ""
    if (data['success']){
        for (const [key, value] of Object.entries(data['data'])){
            let li = document.createElement("li");
            li.innerHTML = value['date'] + "<br>" + value['home'] + " vs. " + value['away'] + " (" + value['home-score'] + " - " + value['away-score'] + ")"
            loadList.appendChild(li)
        }
    } else {
        let li = document.createElement("li");
        li.innerText = "Nincs több megjeleníthető lejátszott meccs a kedvenceket is figyelembe véve!" 
        loadList.appendChild(li)
    }
}