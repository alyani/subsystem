document.addEventListener("DOMContentLoaded", function() {
    const element = document.querySelectorAll(".control-label");
    element.forEach(myFunction);

    function myFunction(item) {
        item.innerHTML = item.innerHTML.replace("*", `<span class="text-danger text-bold">*</span>`);
    }
});
