const input = document.getElementById("q");
input.setAttribute('size', input.getAttribute('placeholder').length);


sortTable = (index, order) => {
    const rows = Array.from(document.querySelectorAll("table tbody tr"));
    const tbody = document.querySelector("table tbody");
    let return1 = (order == "asc") ? -1 : 1;
    let return2 = (order == "asc") ? 1 : -1;

    rows.sort((a, b) => {
        let aCompare = a.childNodes[index].innerText;
        let bCompare = b.childNodes[index].innerText

        if (!isNaN(aCompare) && !isNaN(bCompare)) {
            aCompare = Number(aCompare);
            bCompare = Number(bCompare);
        }


        if (aCompare < bCompare)
            return return1;
        if (aCompare > bCompare)
            return return2;
        return 0;
    });

    tbody.innerHTML = "";

    rows.forEach(item => {
        tbody.appendChild(item);

    })



}


sort = (e) => {
    if (e) {
        const headers = document.querySelectorAll("table thead td");
        const clickedItem = e.target;
        const asc = "(ros.)";
        const desc = "(mal.)";

        const indexOfClickedItem = Array.prototype.indexOf.call(headers, clickedItem)

        headers.forEach(header => {
            if (header == clickedItem) {
                return;
            }
            header.innerText = header.innerText.replace(asc, "");
            header.innerText = header.innerText.replace(desc, "");
        })

        if (!clickedItem.innerText.includes(asc) && !clickedItem.innerText.includes(desc)) {
            clickedItem.innerText += ` ${asc}`;
            sortTable(indexOfClickedItem, "asc");
            return;
        }
        if (clickedItem.innerText.includes(asc)) {
            clickedItem.innerText = clickedItem.innerText.replace(asc, "");
            clickedItem.innerText += ` ${desc}`;
            sortTable(indexOfClickedItem, "desc");
            return;
        }
        if (clickedItem.innerText.includes(desc)) {
            clickedItem.innerText = clickedItem.innerText.replace(desc, "");
            clickedItem.innerText += ` ${asc}`;
            sortTable(indexOfClickedItem, "asc");
            return;
        }
    }



}

document.querySelectorAll("table thead td").forEach((item) => {
    item.addEventListener("click", sort);
})