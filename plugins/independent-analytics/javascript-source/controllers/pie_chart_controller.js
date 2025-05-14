import {Controller} from "@hotwired/stimulus"
import corsairPlugin from '../chart_plugins/corsair_plugin'
import {Chart, registerables} from 'chart.js'
import color from 'color'
import {isDarkMode} from "../utils/appearance";

Chart.register(...registerables)

export default class extends Controller {
    static targets = ['canvas']

    static values = {
        data: Array,
        locale: String,
    }

    connect() {
        this.renderChart()
    }

    renderChart() {
        const labels = this.dataValue.map((item) => item.label)
        const values = this.dataValue.map((item) => item.value)

        const data = {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#7B5BB3',
                    '#7FBAFD',
                    '#8ADBB0',
                    '#FD799E',
                    '#FFEA9C',
                ],
                hoverOffset: 4
            }]
        }

        const config = {
            type: 'pie',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                borderColor:  isDarkMode() ? '#363040' : '#ffffff' ,
                plugins: {
                    legend: {
                        position: 'left',
                        labels: {
                            color: isDarkMode() ? '#ffffff' : '#6D6A73',
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: (tooltips) => {
                                const tooltip = tooltips[0]
                                const label = tooltip.label || ''

                                return label
                            },
                            label: (tooltip) => {
                                const chart = tooltip.chart
                                const data = this.dataValue[tooltip.dataIndex]
                                const total = this.dataValue.reduce((total, dataset, index) => {
                                    if(!chart.getDataVisibility(index)) {
                                        return total;
                                    }

                                    return total + dataset.value
                                }, 0)

                                return ` ${data.formatted_value ?? data.value} ${data.unit} (${this.formatPercent(data.value, total)})`
                            },
                        }
                    }
                }
            },
            data: data,
        }

        if(!this.chart) {
            this.chart = new Chart(this.canvasTarget, config)
        }
    }

    formatPercent(value, total) {
        const number = value > 0 ? value / total : 0
        const asPercent = new Intl.NumberFormat(this.localeValue, {
            style: "percent",
        })

        return asPercent.format(number)
    }
}