import Axios from 'axios';
var Chart = require('chart.js');

var month = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

   var ctx = document.getElementById('myChart');
    const data = [];
    Axios.get(`${localStorage.getItem('url')}/api/v1/information/sales_of_month`).then((response) => {
        const info = response.data.data;
        for (let i of info) {
            data.push(parseFloat(i.totals))
        }
        var myChart  =  new Chart(ctx, {
            type: 'bar',
            data: {
                labels: month,
                datasets: [{
                    label: '# of Votes',
                    data: data,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                animation: {
                    duration: 0
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });


var ctxProduct = document.getElementById('ctxProduct');


function createColors(quantity) {
    let colors = [];
    for(let i = 0; i < quantity; i++) {
        colors[i] = `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.5)`;
    }
    return colors;
}

Axios.get(`${this.url}/api/v1/information/sales_of_product`).then((response) => {
    const info = response.data.data;
    const totals = [];
    const name = [];
    let data = [];
    let colors  = [];
    for (let i of info[0]) {
        name.push(i.name);
    }

    for (let i of this.info) {
        let total = 0;
        for (let l of i) {
            total += parseInt(l.totals);
        }
        totals.push(total);
    }

    let m = 0;


    colors = createColors(info[0].length);

    for (let l = 0; l < info[0].length; l++) {
        const objeto = {
            data: []
        };
        for (let i of info) {
            objeto.label = i[l].name;
            objeto.stack = i[l].name;
            objeto.backgroundColor = colors[m];
            objeto.data.push(parseFloat(i[l].totals));
        }
        m++;
        data.push(objeto)
    }


    var myChart  =  new Chart(ctxProduct, {
        type: 'bar',
        data: {
            labels: month,
            datasets: data
        },
        options: {
            animation: {
                duration: 0
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });


}).catch((error) => {
    console.log(error);
});


    setTimeout(function() {
    Axios.post(`${localStorage.getItem('url')}/api/v1/information/images`, {
        salesOfMonth: ctx.toDataURL(),
        salesOfProduct: ctxProduct.toDataURL(),
    }).then((response) => {

    });
}, 100);




