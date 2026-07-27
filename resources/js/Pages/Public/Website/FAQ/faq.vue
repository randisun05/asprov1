<template>


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
         <div class="row col-md-12">
            <div v-for="(data, index) in datas.data" :key="index">
                <div class="d-flex gap-1">
                    <a class="card card-body w-100" style="background-color: azure;" data-bs-toggle="collapse" :href="'#faq' + data.id" role="button" aria-expanded="false" aria-controls="collapseExample">
                     {{ data.sub }}
                    </a>
                </div>
                <div class="collapse mb-3" :id="'faq' + data.id">
                    <div class="card card-body">
                        {{ data.subitem }}
                    </div>
                </div>
            </div>
                <!-- <p class="d-inline-flex gap-1">
                    <a class="card card-body" style="background-color: azure;" data-bs-toggle="collapse" href="#faq1" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Bagaimana apabila tidak melakukan pembayaran keanggotaan Aspro SDMA?
                    </a>
                </p>
                <div class="collapse mb-3" id="faq1">
                    <div class="card card-body">
                        Pemangku jabatan Analis SDM Aparatur dan Pranata SDM Aparatur tidak mendapatkan nomor keanggotaan dan akses masuk halaman website asprosdma.id
                    </div>
                </div>

                <p class="d-inline-flex gap-1">
                <a class="card card-body" style="background-color: azure;" data-bs-toggle="collapse" href="#faq2" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Apa kegunaan dari nomor anggota Aspro SDMA?
                </a>
                </p>
                <div class="collapse mb-3" id="faq2">
                    <div class="card card-body">
                        Nomor anggota adalah identitas pengenal Pejabat Fungsional SDM Aparatur di dalam organisasi profesi Aspro SDMA. Pejabat fungsional yang sudah memiliki nomor keanggotaan akan menjadi prioritas dalam setiap layanan Aspro SDMA.
                    </div>
                </div>

                <p class="d-inline-flex gap-1">
                <a class="card card-body" style="background-color: azure;" data-bs-toggle="collapse" href="#faq3" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Apakah PPPK dapat bergabung ke dalam Aspro SDMA?
                </a>
                </p>
                <div class="collapse mb-3" id="faq3">
                    <div class="card card-body">
                        Aspro SDMA merupakan wadah organisasi profesi para pejabat fungsional SDMA baik PNS maupun PPPK.
                    </div>
                </div>

                <p class="d-inline-flex gap-1">
                <a class="card card-body" style="background-color: azure;" data-bs-toggle="collapse" href="#faq4" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Apakah benefit bergabung menjadi anggota Aspro SDMA?
                </a>
                </p>
                <div class="collapse mb-3" id="faq4">
                    <div class="card card-body">
                        Dengan bergabung menjadi anggota Aspro SDMA berarti berkontribusi langsung dalam mengembangkan Jabatan Fungsional SDM Aparatur serta memliki nomor keanggotaan dan akses website yang berisi layanan organisasi.
                    </div>
                </div>

                <p class="d-inline-flex gap-1">
                <a class="card card-body" style="background-color: azure;" data-bs-toggle="collapse" href="#faq5" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Iuran keanggotaan digunakan untuk apa?
                </a>
                </p>
                <div class="collapse mb-3" id="faq5">
                    <div class="card card-body">
                        Iuran keanggotaan merupakan salah satu sumber dana Aspro SDMA yang digunakan dalam pelaksanaan program kerja untuk mencapai tujuan organisasi Aspro SDMA.
                    </div>
                </div>

                <p class="d-inline-flex gap-1">
                <a class="card card-body" style="background-color: azure;" data-bs-toggle="collapse" href="#faq6" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Kapan perlu membayar iuran keanggotaan?
                </a>
                </p>
                <div class="collapse mb-3" id="faq6">
                    <div class="card card-body">
                        Iuran keanggotaan dibayarkan pada saat pertama kali melakukan pendaftaran keanggotaan dan dibayarkan setiap tahun.
                    </div>
                </div>

                <p class="d-inline-flex gap-1">
                <a class="card card-body" style="background-color: azure;" data-bs-toggle="collapse" href="#faq7" role="button" aria-expanded="false" aria-controls="collapseExample">
                    Apa itu Aspro SDMA Cabang?
                </a>
                </p>
                <div class="collapse mb-3" id="faq7">
                    <div class="card card-body">
                        Aspro SDMA Cabang berkedudukan di wilayah Kantor Regional BKN dan/atau Instansi Pemerintah yang dibentuk mempertimbangan penguatan organisasi dengan prinsip efektivitas dan efisiensi.
                    </div>
                </div> -->
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
                datas: Object
           },



       }

   </script>
   <style>
    .plus-sign {
        margin-left: auto;
        font-size: 1.5rem;
        color: black;
    }
</style>
