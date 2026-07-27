<template>

    <Head>
        <title>{{ title }}</title>
    </Head>

 <!--page Header-->
<section class="page-header parallaxie padding_top center-block">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <div class="page-titles text-center">
               <h2 class="whitecolor font-light bottom30">{{ title }}</h2>
               <ul class="breadcrumb justify-content-center">
                 <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                 <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>
<!--page Header ends-->


<section id="our-testimonial" class="padding_m">
   <div class="container">
      <div class="row">
         <div class="col-md-12 col-sm-12 text-center">
            <div class="heading-title bottom30">
               <span class="mb-3"><h2>Asosiasi Profesi Jabatan Fungsional SDM Aparatur</h2></span>
         </div>
         <div class="image hover-effect bottom30">
                <!-- <div><img alt="SEO" src="/assets/images/tentang.jpg"></div> -->
                <img v-if="data.image" :src="getImageUrl(data.image)" alt="Gambar" />
               </div>
         </div>
         <div style="text-align: justify;">
          <div v-html="data.body"></div>
          <!-- <p class="h6 bottom20 darkcolor mb-4">Atas berkat rahmat Tuhan Yang Maha Esa, bangsa Indonesia dalam mengisi kemerdekaan dapat hidup damai dalam tatanan global yang dinamis, dengan memanfaatkan seluruh sumber daya yang dimiliki guna mewujudkan masyarakat yang adil, makmur, dan sejahtera berdasarkan Pancasila dan Undang-Undang Dasar 1945. Untuk itu, diperlukan upaya pembaruan yang terus-menerus, baik secara perorangan, kelompok, maupun kerjasama yang didorong keyakinan serta tanggung jawab untuk ikut serta dalam meningkatkan kualitas aparatur sebagai pengawal pembangunan.</p>
            <p class="h6 bottom20 darkcolor mb-4">Mengingat dinamika masyarakat Indonesia, paradigma pelayanan publik ditumbuhkembangkan agar dapat memenuhi kewajiban dasar aparatur dalam melaksanakan tugasnya. Jabatan fungsional Sumber Daya Manusia Aparatur (SDMA) sebagai insan Aparatur Pemerintah, ikut bertanggung jawab untuk mengembangkan melalui pengayaan ilmu pengetahuan dan teknologi yang selaras dengan nilai moral dan etika bangsa Indonesia, sehingga dalam pemberian pelayanan publik bersikap profesional.
            </p>
            <p class="h6 bottom20 darkcolor mb-4">Untuk mengoptimalkan peran dalam menjawab tantangan, sumber daya manusia Indonesia harus memiliki etos kerja. Analis dan Pranata SDMA perlu bersatu dalam satu wadah organisasi profesi yang mandiri dan independen untuk ikut berpartisipasi dalam pembangunan Bangsa dan Negara, maka dibentuklah Organisasi Profesi yang diberi nama Asosiasi Profesi Jabatan Fungsional Sumber Daya Aparatur (Aspro SDMA) yang berdasarkan Pancasila dan Undang-Undang Dasar 1945.
            </p>
            <p class="h6 bottom20 darkcolor mb-4">Melalui organisasi profesi, pejabat fungsional SDMA dapat tumbuh berkembang bersama menghadirkan pendekatan-pendekatan yang mutakhir serta mewujudkan sumber daya manusia aparatur yang kapabel dan berdaya saing. Organisasi profesi jabatan fungsional SDMA merupakan bagian yang tidak terpisahkan dari instansi pembina dalam hal ini Badan Kepegawaian Negara (BKN), oleh karena itu organisasi profesi ini bertujuan memberikan kontribusi sebesar-besarnya bagi pencapaian kinerja penyelenggaraan manajemen aparatur sipil negara dan sumber daya manusia aparatur lainnya. Diharapkan melalui wadah organisasi profesi para pejabat fungsional SDMA dapat berkiprah dengan rahmat Tuhan Yang Maha Esa serta didorong oleh keinginan luhur untuk mewujudkan cita-cita bangsa dalam membentuk Aparatur Sipil Negara yang profesional dan berintegritas, maka dalam pelaksanaannya akan ditentukan pada Anggaran Dasar yang ditetapkan dalam Musyawarah Aspro SDMA sebagai wadah tertinggi dalam menetapkan kebijakan organisasi.
            </p>
            <p class="h6 bottom20 darkcolor mb-4">Pendirian Perkumpulan Asosiasi Profesi Jabatan Fungsional Sumber Daya Manusia Aparatur telah disahkan oleh Menteri Hukum dan HAM RI melalui Keputusan Nomor AHU-0008845.AH.01.07 Tahun 2024 tanggal 25 September 2024.
            </p> -->

         </div>

         </div>
   </div>
</section>
<!--Testimonials Ends-->


</template>

<script>
    //import layout
    import LayoutWebsite from '../../../../Layouts/Website.vue';


    //import reactive
    import {
        reactive
    } from 'vue'

     //import Head from Inertia
     import {
        Head
    } from '@inertiajs/vue3';

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

    export default {

        //layout
        layout: LayoutWebsite,

        //register component
        components: {
            Head,

        },

        //props
        props: {
            title:Object,
            data: Object
        },

         //inisialisasi composition API
    setup() {


                // Method to get the URL of the document
                const getImageUrl = (imageName) => {
                    return `/storage/${imageName}`;
                }


                //return
                return {
                    getImageUrl,
                }
            }



    }

</script>
