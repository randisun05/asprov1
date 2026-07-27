<template>
  <Head>
    <title>Administrator</title>
  </Head>
  <div class="container-fluid padding px-5">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-6 col-12 mb-2">
            <Link href="/admin/members" class="btn btn-md btn-primary border-0 shadow mb-3 me-2" type="button"><i class="fa fa-arrow-left" aria-hidden="true"></i>
                Kembali</Link>
                <a :href="`/admin/members/report/export`" target="_blank" class="btn btn-md btn-success border-0 shadow mb-3 me-2" type="button" ><i class="fa fa-download" aria-hidden="true"></i>
                    Download Laporan</a>
                    <button @click="exportToPDF" class="btn btn-md btn-danger border-0 shadow mb-3" type="button">
              <i class="fa fa-file-pdf" aria-hidden="true"></i> Export to PDF
            </button>
            <a :href="`/admin/members/report/recap`" target="_blank" class="btn btn-md btn-info border-0 shadow mb-3 me-2 text-white ms-2" type="button">
            <i class="fa fa-file-excel" aria-hidden="true"></i> Download Rekap
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-1">
      <div class="col-md-12">
        <div class="card border-0 shadow">
          <div class="card-body" ref="reportContent">
            <BarChart :chartData="chartDataByMonth" :chartOptions="chartOptions" />
            <BarChart :chartData="chartDataByPosition" :chartOptions="chartOptions" />
            <BarChart :chartData="chartDataByLevel" :chartOptions="chartOptions" />
            <BarChart :chartData="chartDataAccumulated" :chartOptions="chartOptions" />
            <BarChart :chartData="chartDataByGender" :chartOptions="chartOptions" />
            <BarChart :chartData="chartDataByType" :chartOptions="chartOptions" />
            <BarChart :chartData="chartDataByRegion" :chartOptions="chartOptions" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, reactive, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import BarChart from '../../../Components/BarChart.vue';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

export default {
  layout: LayoutAdmin,
  components: {
    Head,
    Link,
    BarChart
  },
  props: {
    errors: Object,
    countsPerMonth: {
      type: Object,
      required: true,
      default: () => ({})
    },
    errors: Object,
    dataCountsByPosition: {
      type: Object,
      required: true,
      default: () => ({})
    },
    dataCountsByLevel: {
      type: Object,
      required: true,
      default: () => ({})
    },
    accumulatedCounts: {
      type: Object,
      required: true,
      default: () => ({})
    },
    dataCountsByGender: {
      type: Object,
      required: true,
      default: () => ({})
    },
    dataCountsByType: {
      type: Object,
      required: true,
      default: () => ({})
    },
    dataCountsByRegion: {
      type: Object,
      required: true,
      default: () => ({})
    }
  },
  setup(props) {
    const reportContent = ref(null);
  const chartOptions = reactive({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      datalabels: {
        color: '#000', // You can customize the label color
        anchor: 'end',
        align: 'end',
        font: {
          weight: 'bold',
          size: 12
        },
        formatter(value) {
          return value; // Show the value directly
        }
      }
    }
  });

  const chartDataByMonth = reactive({
    labels: Object.keys(props.countsPerMonth).map(key => `${key}`),
    datasets: [
      {
        label: 'Counts By Month',
        backgroundColor: '#FFA500',
        data: Object.values(props.countsPerMonth)
      }
    ]
  });

  const chartDataByPosition = reactive({
    labels: Object.keys(props.dataCountsByPosition).map(key => `${key}`),
    datasets: [
      {
        label: 'Counts By Jabatan',
        backgroundColor: '#f87979',
        data: Object.values(props.dataCountsByPosition)
      }
    ]
  });

  const chartDataByLevel = reactive({
    labels: Object.keys(props.dataCountsByLevel).map(key => `${key}`),
    datasets: [
      {
        label: 'Counts By Jenjang',
        backgroundColor: '#7acbf9',
        data: Object.values(props.dataCountsByLevel)
      }
    ]
  });

  const chartDataByGender = reactive({
    labels: Object.keys(props.dataCountsByGender).map(key => `${key}`),
    datasets: [
      {
        label: 'Counts By Gender',
        backgroundColor: '#f9a57a',
        data: Object.values(props.dataCountsByGender)
      }
    ]
  });

  const chartDataByType = reactive({
    labels: Object.keys(props.dataCountsByType).map(key => `${key}`),
    datasets: [
      {
        label: 'Counts By Tipe Instansi',
        backgroundColor: '#a57af9',
        data: Object.values(props.dataCountsByType)
      }
    ]
  });

  const chartDataByRegion = reactive({
    labels: Object.keys(props.dataCountsByRegion).map(key => `${key}`),
    datasets: [
      {
        label: 'Counts By Region',
        backgroundColor: '#7af9d4',
        data: Object.values(props.dataCountsByRegion)
      }
    ]
  });

  const chartDataAccumulated = reactive({
    labels: Object.keys(props.accumulatedCounts).map(key => `${key}`),
    datasets: [
      {
        label: 'Accumulation by Month',
        backgroundColor: '#79f879',
        data: Object.values(props.accumulatedCounts)
      }
    ]
  });

  const exportToPDF = () => {
  if (reportContent.value) {
    html2canvas(reportContent.value, { scale: 2 }).then(canvas => {
      const imgData = canvas.toDataURL('image/png');
      const pdf = new jsPDF('p', 'mm', 'a4');

      const pdfWidth = pdf.internal.pageSize.getWidth();
      const pdfHeight = pdf.internal.pageSize.getHeight();

      const imgWidth = canvas.width;
      const imgHeight = canvas.height;

      // Rasio tinggi dan lebar dari gambar yang dihasilkan
      const ratio = imgWidth / imgHeight;

      // Menghitung tinggi gambar dalam PDF berdasarkan lebar halaman PDF
      const pdfImgHeight = pdfWidth / ratio;

      let position = 0;
      let heightLeft = pdfImgHeight;

      if (pdfImgHeight > pdfHeight) {
        // Gambar lebih tinggi dari satu halaman, bagi ke dalam beberapa halaman
        while (heightLeft > 0) {
          pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, pdfImgHeight);
          heightLeft -= pdfHeight;
          position -= pdfHeight;

          // Jika ada sisa konten, tambahkan halaman baru
          if (heightLeft > 0) {
            pdf.addPage();
          }
        }
      } else {
        // Jika gambar pas di satu halaman
        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfImgHeight);
      }

      pdf.save('report.pdf');
    });
  } else {
    console.error('reportContent is not available');
  }
};


  return {
    reportContent,
    chartDataByPosition,
    chartDataByLevel,
    chartDataAccumulated,
    chartOptions,
    chartDataByMonth,
    exportToPDF,
    chartDataByGender,
    chartDataByType,
    chartDataByRegion
  };
}

};
</script>

<style>

</style>
