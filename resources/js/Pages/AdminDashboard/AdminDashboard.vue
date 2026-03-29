<script setup>
import { onMounted, ref, watch, reactive } from 'vue'
import { Chart } from 'chart.js/auto'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
  genderStats: Object,
  revenueStats: Array,
  countriesStats: Array,
  topClients: Array,
  currentYears: Object,
  availableYears: Array
})

const genderChartRef = ref(null)
const revenueChartRef = ref(null)
const countriesChartRef = ref(null)
const clientsChartRef = ref(null)

const years = reactive({
  gender: props.currentYears.gender,
  revenue: props.currentYears.revenue,
  countries: props.currentYears.countries,
  clients: props.currentYears.clients
})

const fetchYearData = () => {
  router.get(window.location.pathname, {
    gender_year: years.gender,
    revenue_year: years.revenue,
    countries_year: years.countries,
    clients_year: years.clients
  }, { preserveState: true, replace: true })
}

let genderChart, revenueChart, countriesChart, clientsChart

const initCharts = () => {
  const pieOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
      }
    },
    layout: { padding: { bottom: 10 } }
  }

  genderChart = new Chart(genderChartRef.value, {
    type: 'pie',
    data: {
      labels: ['Male', 'Female'],
      datasets: [{
        data: [props.genderStats.male, props.genderStats.female],
        backgroundColor: ['#0ea5e9', '#f472b6']
      }]
    },
    options: pieOptions
  })

  revenueChart = new Chart(revenueChartRef.value, {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label: `Revenue ${props.currentYears.revenue}`,
        data: props.revenueStats,
        borderColor: '#10b981',
        backgroundColor: 'rgba(16,185,129,0.1)',
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
        }
      },
      scales: { y: { beginAtZero: true } },
      layout: { padding: { bottom: 10 } }
    }
  })

  countriesChart = new Chart(countriesChartRef.value, {
    type: 'pie',
    data: {
      labels: props.countriesStats.map(c => c.country),
      datasets: [{
        data: props.countriesStats.map(c => c.count),
        backgroundColor: ['#0ea5e9','#10b981','#f59e0b','#8b5cf6','#ef4444','#14b8a6','#f97316','#6366f1','#3b82f6','#84cc16']
      }]
    },
    options: pieOptions
  })

  clientsChart = new Chart(clientsChartRef.value, {
    type: 'pie',
    data: {
      labels: props.topClients.map(c => c.name),
      datasets: [{
        data: props.topClients.map(c => c.total),
        backgroundColor: ['#0ea5e9','#10b981','#f59e0b','#8b5cf6','#ef4444','#14b8a6','#f97316','#6366f1','#22c55e','#eab308']
      }]
    },
    options: pieOptions
  })
}

const updateCharts = () => {
  if (genderChart) {
    genderChart.data.datasets[0].data = [props.genderStats.male, props.genderStats.female]
    genderChart.update()
  }
  if (revenueChart) {
    revenueChart.data.datasets[0].label = `Revenue ${props.currentYears.revenue}`
    revenueChart.data.datasets[0].data = props.revenueStats
    revenueChart.update()
  }
  if (countriesChart) {
    countriesChart.data.labels = props.countriesStats.map(c => c.country)
    countriesChart.data.datasets[0].data = props.countriesStats.map(c => c.count)
    countriesChart.update()
  }
  if (clientsChart) {
    clientsChart.data.labels = props.topClients.map(c => c.name)
    clientsChart.data.datasets[0].data = props.topClients.map(c => c.total)
    clientsChart.update()
  }
}

onMounted(() => initCharts())

watch(() => props, () => updateCharts(), { deep: true })

defineOptions({ layout: AdminLayout })
</script>

<template>
  <div class="p-6 space-y-6">
    <div class="flex items-end justify-between">
      <div>
        <h1 class="text-2xl font-bold">Dashboard Analytics</h1>
        <p class="text-sm text-muted-foreground">Overview of reservations</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <Card>
        <CardHeader class="flex flex-row justify-between items-start">
          <div>
            <CardTitle>Male vs Female</CardTitle>
            <p class="text-sm text-muted-foreground">Reservation distribution</p>
          </div>
          <Select v-model="years.gender" @update:modelValue="fetchYearData">
            <SelectTrigger class="w-[100px]"><SelectValue /></SelectTrigger>
            <SelectContent>
              <SelectItem v-for="year in availableYears" :key="year" :value="year">{{ year }}</SelectItem>
            </SelectContent>
          </Select>
        </CardHeader>
        <CardContent class="h-[320px]"><canvas ref="genderChartRef"></canvas></CardContent>
      </Card>

      <Card>
        <CardHeader class="flex flex-row justify-between items-start">
          <div>
            <CardTitle>Revenue</CardTitle>
            <p class="text-sm text-muted-foreground">Monthly income ({{ props.currentYears.revenue }})</p>
          </div>
          <Select v-model="years.revenue" @update:modelValue="fetchYearData">
            <SelectTrigger class="w-[100px]"><SelectValue /></SelectTrigger>
            <SelectContent>
              <SelectItem v-for="year in availableYears" :key="year" :value="year">{{ year }}</SelectItem>
            </SelectContent>
          </Select>
        </CardHeader>
        <CardContent class="h-[320px]"><canvas ref="revenueChartRef"></canvas></CardContent>
      </Card>

      <Card>
        <CardHeader class="flex flex-row justify-between items-start">
          <div>
            <CardTitle>Countries</CardTitle>
            <p class="text-sm text-muted-foreground">Reservations by country</p>
          </div>
          <Select v-model="years.countries" @update:modelValue="fetchYearData">
            <SelectTrigger class="w-[100px]"><SelectValue /></SelectTrigger>
            <SelectContent>
              <SelectItem v-for="year in availableYears" :key="year" :value="year">{{ year }}</SelectItem>
            </SelectContent>
          </Select>
        </CardHeader>
        <CardContent class="h-[320px]"><canvas ref="countriesChartRef"></canvas></CardContent>
      </Card>

      <Card>
        <CardHeader class="flex flex-row justify-between items-start">
          <div>
            <CardTitle>Top Clients</CardTitle>
            <p class="text-sm text-muted-foreground">Top 10 customers</p>
          </div>
          <Select v-model="years.clients" @update:modelValue="fetchYearData">
            <SelectTrigger class="w-[100px]"><SelectValue /></SelectTrigger>
            <SelectContent>
              <SelectItem v-for="year in availableYears" :key="year" :value="year">{{ year }}</SelectItem>
            </SelectContent>
          </Select>
        </CardHeader>
        <CardContent class="h-[320px]"><canvas ref="clientsChartRef"></canvas></CardContent>
      </Card>

    </div>
  </div>
</template>
