/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/global.scss';
import IMask from 'imask';
import Core from "./app/core.js";
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-responsive-dt';

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

const App = new Core();
let linkButtons = App.findBy('.toggle-election');

let tableOptions = {
    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
}


let table = new DataTable('#table', {
    responsive: true,
    "language": tableOptions
});

createLinkAction(linkButtons,setLike,'href','data-value')

function createLinkAction(elements,customFunction,attribute,id, eventAction = "click"){
    Array.prototype.slice.call(elements)
        .forEach(function (element) {
            let link = App.getAttribute(element,attribute);
            let value = App.getAttribute(element,id);

            element.addEventListener(eventAction,function(e) {
                    e.preventDefault();
                    customFunction(link,value);
                },
                false)
        })
}

function loadPage(link) {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        App.findOneBy('#sample_items').innerHTML = this.responseText;
    }
    xhttp.open("POST", link);
    xhttp.send();
    return null
}

function setLike(link, value){
    let likeIcon = App.findOneBy('.like-icon-'+value);
    let likeLoader = App.findOneBy('.like-loader-'+value);
    App.setClass(likeIcon,'d-none');
    App.setClass(likeLoader,'d-none',true);
    let data = {};
    data.id = value;
    let json = JSON.stringify(data);
    let xhr = new XMLHttpRequest();
    xhr.open('post', link,true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        if (xhr.status === 200) {
            let response = JSON.parse(xhr.responseText);
            App.setClass(likeIcon,'d-none',true);
            App.setClass(likeLoader,'d-none');
            if(response.electionActive)
            {
                App.setClass(likeIcon,'bi-toggle-on');
                App.setClass(likeIcon,'bi-toggle-off',true);
            } else {
                App.setClass(likeIcon,'bi-toggle-off');
                App.setClass(likeIcon,'bi-toggle-on',true);
            }
        }
    };
    xhr.send(json);
}


(() => {
    'use strict'
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    const element = document.getElementById('code');
    const maskOptions = {
        mask: '[aaaaa]',
        prepareChar: str => str.toUpperCase(),
    };
    const mask = IMask(element, maskOptions);

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
    })

})()

