<template>
    <div class="w-full px-4 md:px-0 md:mt-8 mb-16 text-gray-800 leading-normal">
        <div class="flex flex-row flex-wrap flex-grow mt-2">

            <div class="w-full p-3">
                <!--Graph Card-->
                <div class="bg-gray-900 border border-gray-800 rounded shadow">
                    <div class="border-b border-gray-800 p-3">
                        <h5 class="font-bold uppercase text-gray-600">{{ this.$route.params.currency }}</h5>
                    </div>
                    <div class="">
                        <canvas id="chart" class="chartjs"></canvas>
                    </div>
                    <div class="w-full">
                        <div class="flex flex-wrap m-2">
                            <div class="w-full md:w-1/4 px-3 m2-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-white text-xs font-bold mb-2" for="type">
                                    Bar Type:
                                </label>
                                <select id="type" v-on:change="update()" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option value="candlestick" selected>Candlestick</option>
                                    <option value="ohlc">OHLC</option>
                                </select>
                            </div>
                            <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-white text-xs font-bold mb-2" for="scale-type">
                                    Scale Type:
                                </label>
                                <select id="scale-type" v-on:change="update()" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option value="linear" selected>Linear</option>
                                    <option value="logarithmic">Logarithmic</option>
                                </select>
                            </div>
                            <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-white text-xs font-bold mb-2" for="color-scheme">
                                    Color Scheme:
                                </label>
                                <select id="color-scheme" v-on:change="update()" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option value="muted" selected>Muted</option>
                                    <option value="neon">Neon</option>
                                </select>
                            </div>
                            <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-white text-xs font-bold mb-2" for="border">
                                    Border:
                                </label>
                                <select id="border" v-on:change="update()" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option value="true" selected>Yes</option>
                                    <option value="false">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/Graph Card-->
            </div>
        </div>
    </div>
</template>
<script>
  import * as DateTime from 'luxon';

  export default {
    data() {
      return {
        chart: null,
        chartBar: {
          date: null,
          o: 0,
          h: 0,
          l: 0,
          c: 0,
        },
        barCount: 60,
        initialDateStr: '01 Apr 2017 00:00 Z'
      }
    },
    methods: {
      createChart(data){
        this.chart = new Chart(document.getElementById('chart').getContext('2d'), {
          type: 'candlestick',
          data: {
            datasets: [{
              label: this.$route.params.currency.toUpperCase() + ' - Price chart',
              data: data
            }]
          },
          options: {
            scales: {
              x: {
                afterBuildTicks: function(scale) {
                  const majorUnit = scale._majorUnit;
                  const ticks = scale.ticks;
                  const firstTick = ticks[0];
                  let i, ilen, val, tick, currMajor, lastMajor;

                  val = luxon.DateTime.fromMillis(ticks[0].value);
                  firstTick.major = (majorUnit === 'minute' && val.second === 0)
                      || (majorUnit === 'hour' && val.minute === 0)
                      || (majorUnit === 'day' && val.hour === 9)
                      || (majorUnit === 'month' && val.day <= 3 && val.weekday === 1)
                      || (majorUnit === 'year' && val.month === 0);
                  lastMajor = val.get(majorUnit);

                  for (i = 1, ilen = ticks.length; i < ilen; i++) {
                    tick = ticks[i];
                    val = luxon.DateTime.fromMillis(tick.value);
                    currMajor = val.get(majorUnit);
                    tick.major = currMajor !== lastMajor;
                    lastMajor = currMajor;
                  }
                  return ticks;
                }
              }
            }
          }
        });
      },
      update(data = null){
        let dataset = this.chart.config.data.datasets[0];

        // candlestick vs ohlc
        let type = document.getElementById('type').value;
        dataset.type = type;

        if(data !== null){
          this.chart.config.data = data;
        }

        // linear vs log
        let scaleType = document.getElementById('scale-type').value;
        this.chart.config.options.scales.y.type = scaleType;

        // color
        let colorScheme = document.getElementById('color-scheme').value;
        if (colorScheme === 'neon') {
          dataset.color = {
            up: '#01ff01',
            down: '#fe0000',
            unchanged: '#999',
          };
        } else {
          delete dataset.color;
        }

        // border
        let border = document.getElementById('border').value;
        let defaultOpts = Chart.defaults.elements[type];
        if (border === 'true') {
          dataset.borderColor = defaultOpts.borderColor;
        } else {
          dataset.borderColor = {
            up: defaultOpts.color.up,
            down: defaultOpts.color.down,
            unchanged: defaultOpts.color.up
          };
        }

        this.chart.update();
      },
      getData(baseSymbol, quoteSymbol, interval, callback){
        let data = [];
        const startTime = DateTime.DateTime.local().setZone('America/New_York').minus({ month: 2 }).toISO();
        const proxyurl = "https://cors-anywhere.herokuapp.com/";
        let that = this;
        axios
        .get(proxyurl + `https://dev-api.shrimpy.io/v1/exchanges/coinbasepro/candles?quoteTradingSymbol=${quoteSymbol}&baseTradingSymbol=${baseSymbol}&interval=${interval}&startTime=${startTime}`,
            {
              headers: {
                "Content-Type": "application/json",
                "Access-Control-Allow-Origin": "*",
                "Access-Control-Allow-Methods": "*",
                "Access-Control-Allow-Credentials": "true"
              }
            })
        .then(function(response) {
          for(let d in response.data){
            let bar = response.data[d];
            data.push({
              t: bar.time,
              o: bar.open,
              h: bar.high,
              l: bar.low,
              c: bar.close
            })
          }
          callback(data);
        });
      },
    },
    mounted() {
      let that = this;
      this.getData('BTC', 'USD', '1D', function(data) {
        that.createChart(data);
      });

      /*setInterval(function(){
        that.getData('BTC', 'USD', '1D', function(data){
          if(that.chart !== null){
            that.chart.destroy();
          }
          that.createChart(data);
        })
      }, 20000);*/
    },
    watch:{
      $route (to, from){
        console.log('lol');
      }
    }
  }
</script>
