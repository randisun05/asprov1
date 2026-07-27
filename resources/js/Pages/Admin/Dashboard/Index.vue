<template>

<!-- Our Blogs -->
<section id="dashboard" class="container-fluid px-5">
    <div class="container-fluid padding">
    <div class="row">
      <div class="col-md-8 col-8">
        <h3>Data Monitoring</h3>
      </div>
      <div class="col-md-4 col-4 text-end">
        <button class="btn btn-outline-secondary btn-sm" @click="exportToPDF"><i class="fa fa-file-pdf-o me-1"></i> Export PDF</button>
      </div>
    </div>

    <div ref="reportContent">
        <h5 class="text-muted mb-2">Registrasi Anggota</h5>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2" v-for="tile in registrationTiles" :key="tile.label">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h3 class="mb-0">{{ tile.value }}</h3>
                        <span class="text-muted small">{{ tile.label }}</span>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="text-muted mb-2">Konten & Kegiatan</h5>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4" v-for="tile in publicationTiles" :key="tile.label">
                <div class="card border-0 shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon me-3" :class="tile.colorClass">
                            <i class="fa" :class="tile.icon" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ tile.value }}</h3>
                            <span class="text-muted small">{{ tile.label }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="text-muted mb-2">Data Anggota</h5>
        <div class="row g-3">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h6 class="text-center">Pertumbuhan Anggota per Bulan</h6>
                        <BarChart :chartData="chartDataByMonth" :chartOptions="chartOptions" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h6 class="text-center">Berdasarkan Jabatan</h6>
                        <BarChart :chartData="chartDataByPosition" :chartOptions="chartOptions" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h6 class="text-center">Berdasarkan Jenjang</h6>
                        <BarChart :chartData="chartDataByLevel" :chartOptions="chartOptions" />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h6 class="text-center">Jenis Kelamin</h6>
                        <BarChart :chartData="chartDataByGender" :chartOptions="chartOptions" />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h6 class="text-center">Instansi Pusat / Daerah</h6>
                        <BarChart :chartData="chartDataByType" :chartOptions="chartOptions" />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <h6 class="text-center">Penyebaran Wilayah</h6>
                        <BarChart :chartData="chartDataByRegion" :chartOptions="chartOptions" />
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</section>
<!--Our Blogs Ends-->

</template>

<script>
    //import layout Admin
    import LayoutAdmin from '../../../Layouts/Admin.vue';

    import { Head, Link } from '@inertiajs/inertia-vue3';
    import { ref, reactive, computed, onMounted } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import Swal from 'sweetalert2';
    import BarChart from '../../../Components/BarChart.vue';
    import html2canvas from 'html2canvas';
    import jsPDF from 'jspdf';


    export default {

        //layout
        layout: LayoutAdmin,

        //register components
        components: {
            Head,
            Link,
            BarChart
        },

        //props
        props: {
            registrationData: {
            type: Object,
            required: true,
            default: () => ({})
            },
            publicationData: {
            type: Object,
            required: true,
            default: () => ({})
            },
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
            const registrationLabels = {
                'total-registrasi': 'Total Registrasi',
                'telah-dilakukan-verifikasi': 'Terverifikasi',
                'upload-bukti-pembayaran': 'Bukti Bayar Diunggah',
                'perbaikan': 'Perlu Perbaikan',
                'selesai': 'Disetujui',
                'ditolak': 'Ditolak',
            };
            const registrationTiles = computed(() => Object.entries(props.registrationData).map(([key, value]) => ({
                label: registrationLabels[key] ?? key,
                value,
            })));

            const publicationMeta = {
                'publikasi': { label: 'Publikasi', icon: 'fa-newspaper-o', colorClass: 'bg-primary bg-opacity-10 text-primary' },
                'kegiatan': { label: 'Kegiatan', icon: 'fa-calendar', colorClass: 'bg-success bg-opacity-10 text-success' },
                'merchan': { label: 'Merchandise', icon: 'fa-shopping-bag', colorClass: 'bg-warning bg-opacity-10 text-warning' },
            };
            const publicationTiles = computed(() => Object.entries(props.publicationData).map(([key, value]) => ({
                label: publicationMeta[key]?.label ?? key,
                icon: publicationMeta[key]?.icon ?? 'fa-circle',
                colorClass: publicationMeta[key]?.colorClass ?? 'bg-secondary bg-opacity-10 text-secondary',
                value,
            })));

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

            const chartDataByGender = reactive({
                labels: Object.keys(props.dataCountsByGender).map(key => `${key}`),
                datasets: [
                {
                    label: 'Counts By Gender',
                    backgroundColor: ['#36A2EB', '#FF6384'],
                    data: Object.values(props.dataCountsByGender)
                }
                ]
            });

            const chartDataByType = reactive({
                labels: Object.keys(props.dataCountsByType).map(key => `${key}`),
                datasets: [
                {
                    label: 'Counts By Type',
                    backgroundColor: '#FFCE56',
                    data: Object.values(props.dataCountsByType)
                }
                ]
            });

            const chartDataByRegion = reactive({
                labels: Object.keys(props.dataCountsByRegion).map(key => `${key}`),
                datasets: [
                {
                    label: 'Counts By Region',
                    backgroundColor: '#4BC0C0',
                    data: Object.values(props.dataCountsByRegion)
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
                registrationTiles,
                publicationTiles,
                chartDataByGender,
                chartDataByType,
                chartDataByRegion,
                exportToPDF

            };
            }
        };

</script>

<style>
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
