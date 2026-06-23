

document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".tab").forEach(tab => {

    tab.addEventListener("click", function(){

        document.querySelectorAll(".tab").forEach(t=>{
            t.classList.remove("active")
        })

        document.querySelectorAll(".tab-content").forEach(c=>{
            c.classList.remove("active")
        })

        this.classList.add("active")

        let id = this.dataset.tab
        document.getElementById(id).classList.add("active")

    })

});

});