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
              <span><h2>Program</h2></span>
           </div>
        </div>

        <div class="col-md-4">
           <ul class="nav nav-pills flex-column" id="program-list">
              <li class="nav-item active">
                 <a class="nav-link" :class="{ active: activeTab === 'program1' }" @click="setActiveTab('program1')">Mengelola perubahan data dan profil  anggota Aspro SDMA</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" :class="{ active: activeTab === 'program2' }" @click="setActiveTab('program2')">Memvalidasi data anggota Aspro SDMA</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" :class="{ active: activeTab === 'program3' }" @click="setActiveTab('program3')">Menyajikan informasi data anggota Aspro SDMA</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" :class="{ active: activeTab === 'program4' }" @click="setActiveTab('program4')">Mengelola penyusunan profil keahlian dan dats kategorisasi data terkait   anggota dan wilayah kerja anggota Aspro SDMA</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" :class="{ active: activeTab === 'program5' }" @click="setActiveTab('program5')">Mengendalikan kerahasiaan data anggota Aspro SDMA</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" :class="{ active: activeTab === 'program6' }" @click="setActiveTab('program6')">Melaksanakan dan memfasilitasi kajian bidang manajemen sumber daya  manusia aparatur</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" :class="{ active: activeTab === 'program7' }" @click="setActiveTab('program7')">Memetakan kualitas tata Kelola organisasi</a>
              </li> <li class="nav-item">
                 <a class="nav-link" :class="{ active: activeTab === 'program8' }" @click="setActiveTab('program8')">Mengkaji tren kebutuhan organisasi terkini sebagai bahan evaluasi organisasi</a>
              </li>
           </ul>
        </div>

        <div class="col-md-8">
           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program1'" class="tab-pane fade show active" id="program1">



              </div>
           </div>

           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program2'" class="tab-pane fade show active" id="program2">



              </div>
           </div>

           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program3'" class="tab-pane fade show active" id="program3">



              </div>
           </div>

           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program4'" class="tab-pane fade show active" id="program4">



              </div>
           </div>


           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program5'" class="tab-pane fade show active" id="program5">



              </div>
           </div>



           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program6'" class="tab-pane fade show active" id="program6">



              </div>
           </div>


           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program7'" class="tab-pane fade show active" id="program7">



              </div>
           </div>


           <div class="program-description" id="program-description-1">
              <div v-show="activeTab === 'program8'" class="tab-pane fade show active" id="program8">




              </div>
           </div>
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
           title:Object

       },

       data() {
            return {
              activeTab: 'program1', // Set the default active tab

            };
          },

                  methods: {
            setActiveTab(tabName) {
              this.activeTab = tabName;
            },
          },



    }


</script>
<style>
#program-list .nav-link {
    border-right: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
}
</style>
