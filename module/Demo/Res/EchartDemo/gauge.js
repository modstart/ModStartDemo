option = {
    tooltip: {
        formatter: '{a} <br/>{b} : {c}%'
    },
    series: [
        {
            name: 'Pressure',
            type: 'gauge',
            detail: {
                formatter: '{value}'
            },
            data: [
                {
                    value: 20,
                    name: 'SCORE'
                }
            ]
        }
    ]
}
