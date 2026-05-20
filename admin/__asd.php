<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Highcharts Column Click</title>

    <script src="https://code.highcharts.com/highcharts.js"></script>

    <style>
        #container{
            height:400px;
            width:100%;
        }
    </style>
</head>
<body>

<div id="container"></div>

<script>

    Highcharts.chart('container', {

        chart: {
            type: 'column'
        },

        title: {
            text: 'Column Click Event'
        },

        xAxis: {

            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ]

        },

        plotOptions: {

            series: {

                cursor: 'pointer',

                point: {

                    events: {

                        click: function () {

                            let month = this.category;
                            let value = this.y;
                            let index = this.index;

                            alert(
                                "Month : " + month +
                                "\nValue : " + value +
                                "\nIndex : " + index
                            );

                            console.log("FULL OBJECT => ", this);

                            console.log("Category => ", this.category);
                            console.log("Value => ", this.y);
                            console.log("Index => ", this.index);

                        }

                    }

                }

            }

        },
        series: [{
            name: 'Sales',
            data: [
                29.9,
                71.5,
                106.4,
                129.2,
                144.0,
                176.0,
                135.6,
                148.5,
                216.4,
                194.1,
                95.6,
                54.4
            ]

        }]

    });

</script>

</body>
</html>