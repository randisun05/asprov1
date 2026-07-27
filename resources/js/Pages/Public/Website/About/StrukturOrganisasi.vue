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
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: activeTab === 'pusat' }"
                            @click="setActiveTab('pusat')">Pengurus Pusat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: activeTab === 'pembina' }"
                            @click="setActiveTab('pembina')">Dewan Pembina</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" :class="{ active: activeTab === 'pengawas' }"
                            @click="setActiveTab('pengawas')">Dewan Pengawas</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <!-- Monitoring -->
                <div v-show="activeTab === 'pusat'" class="tab-pane fade show active" id="pusat">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="card border-0 shadow">
                                <div class="card-body">
                                    <hr>

                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-8 offset-md-2 col-sm-12 text-center">
                                                <div class="heading-title">
                                                    <h3 class="darkcolor bottom20">Pengurus</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/ritakardinasari.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Rita Kardinasari</h4>
                                                        <h5>Ketua Umum Aspro SDMA</h5>
                                                        <p>Pemerintah Provinsi Jawa Barat</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/agung.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Agung Sugiarto</h4>
                                                        <h5>Wakil Ketua Umum Aspro SDMA</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/dian.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4 class="">Dian Andriani</h4>
                                                        <h5 class="mt-1">Bendahara I</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/kurnia.jpeg" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4 class="">Kurnia Dianandari</h4>
                                                        <h5 class="mt-1">Bendahara II</h5>
                                                        <p>Pemerintah Provinsi DKI Jakarta</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/diana.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Diana Restiani</h6>
                                                        <p>Anggota Bendahara I</p>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/henniqurratul.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Henni Qurratul Aini</h6>
                                                        <p>Anggota Bendahara II</p>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/endang.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Endang Kristuti</h4>
                                                        <h5>Sekretaris I</h5>
                                                        <p>Badan Riset dan Inovasi Nasional</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/dzikri.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Mohamad Dzikri Fadillah</h6>
                                                        <p>Anggota Sekretaris I</p>
                                                        <p>Badan Riset dan Inovasi Nasional</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/yulia.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Yulia Razila Ningsih</h4>
                                                        <h5>Sekretaris II</h5>
                                                        <p>Pemerintah Provinsi Riau</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                          <div class="row justify-content-center padding_m">
                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/rafrianika.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Rafrianika</h6>
                                                        <p>Anggota Sekretaris II</p>
                                                        <p>Kementerian Agama</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center mt-4">
                                            <div class="col-lg-3 col-md-6">
                                                 <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/asepbudiman.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Asep Budiman</h6>
                                                        <h5>Ketua Bidang Hubungan Masyarakat dan Kerja Sama</h5>
                                                        <p>Institut Seni dan Budaya Indonesia - Bandung</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">


                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/maki.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Moh Maki Munawar</h6>
                                                        <p>Anggota Bidang I</p>
                                                        <p>Pemerintah Provinsi Jawa Barat</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/agusamirudin.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Agus Amirudin</h6>
                                                        <p>Anggota Bidang I</p>
                                                        <p>Kementerian Luar Negeri</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/sabtutik.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Sabtutik Handayani</h6>
                                                        <p>Anggota Bidang I</p>
                                                        <p>Universitas Sebelas Maret</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center mt-4">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/fxsunaryanta.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>FX. Sunaryanta</h4>
                                                        <h5>Ketua Bidang Hukum dan Advokasi</h5>
                                                        <p>Pemerintah Kabupaten Pemalang</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/prita.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Prita Indrianingsih</h6>
                                                        <p>Anggota Bidang II</p>
                                                        <p>Kementerian Imigrasi dan Pemasyarakatan</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/putrirahmalya.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Putri Rahmalya</h6>
                                                        <p>Anggota Bidang II</p>
                                                        <p>Badan Pembinaan Hukum Nasional Kemenkumham</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center mt-4">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/miadian.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Mia Dian Ratnasari</h4>
                                                        <h5>Ketua Bidang Pengembangan Kapasitas Insani</h5>
                                                        <p>Badan Karantina Indonesia</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/iwan.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Iwan Prasetyo</h6>
                                                        <p>Anggota Bidang III</p>
                                                        <p>Lembaga Administrasi Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/eva.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Eva Fadella</h6>
                                                        <p>Anggota Bidang III</p>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                             <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/andysetyawan.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Andy Setyawan</h6>
                                                        <p>Anggota Bidang III</p>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row justify-content-center mt-4">
                                            <div class="col-lg-3 col-md-6">
                                                 <div class="team-box no-hover">
                                                        <div class="image">
                                                            <img src="/assets/images/claudia.png" alt="">
                                                        </div>
                                                        <div class="team-content gradient_bg">
                                                            <h6>Claudia Frederika Margaretha</h6>
                                                            <p>Anggota Bidang IV</p>
                                                            <p>Kementerian Perdagangan</p>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">
                                                <div class="col-lg-2 col-md-6">
                                                    <div class="team-box no-hover">
                                                        <div class="image">
                                                            <img src="/assets/images/harylasmana.png" alt="">
                                                        </div>
                                                        <div class="team-content gradient_bg">
                                                            <h6>Hary Lasmana</h6>
                                                            <p>Anggota Bidang IV</p>
                                                            <p>Kementerian Desa dan PDTT</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 col-md-6">
                                                    <div class="team-box no-hover">
                                                        <div class="image">
                                                            <img src="/assets/images/imasfatimah.png" alt="">
                                                        </div>
                                                        <div class="team-content gradient_bg">
                                                            <h6>Imas Siti Fatimah</h6>
                                                            <p>Anggota Bidang IV</p>
                                                            <p>Kementerian Kesehatan</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <div class="row justify-content-center mt-4">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/dhanu.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Dhanu Sukma Utomo</h4>
                                                        <h5>Ketua Bidang Keanggotaan dan Organisasi</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/dimas.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Dimas Anugrah Dwi Saputra</h6>
                                                        <p>Anggota Bidang V</p>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                           <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/hestiany.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Hestiani</h6>
                                                        <p>Anggota Bidang V</p>
                                                        <p>Kementerian Dalam Negeri</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/afreza.jpeg" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Afreza Zeil Fahmi Aziz</h6>
                                                        <p>Anggota Bidang V</p>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/giri.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h6>Giri Baskoro</h6>
                                                        <p>Anggota Bidang V</p>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-show="activeTab === 'pembina'" class="tab-pane fade show active" id="pembina">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="card border-0 shadow">
                                <div class="card-body">
                                    <hr>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-8 offset-md-2 col-sm-12 text-center">
                                                <div class="heading-title">
                                                    <h2 class="darkcolor bottom20">Dewan Pembina</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/zudan.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Prof. Dr. Zudan Arif Fakrulloh, SH., M.H</h4>
                                                        <h5 class="mt-2">Kepala Badan Kepegawaian Negara</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/yomo.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Drs. Haryomo Dwi Putranto, M.Hum.</h4>
                                                        <h5 class="mt-2">Wakil Kepala Badan Kepegawaian Negara</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/imas.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Hj. Imas Sukmariah, S.Sos., MAP.</h4>
                                                        <h5 class="mt-2">Sekretaris Utama</h5>
                                                        <p>Badan Kepegawaian Negara</p>

                                                    </div>
                                                </div>
                                            </div>

                                             <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/herman.jpeg" alt="" style="width: 85%;">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Dr. Herman, M.Si.</h4>
                                                        <h5 class="mt-2">Deputi Bidang Pembinaan Penyelenggaraan Manajemen ASN</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/aris.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Drs. Aris Windiyanto, M.Si.</h4>
                                                        <h5 class="mt-2">Deputi Bidang Penyelenggaraan Layanan Manajemen ASN</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/suharmen.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Suharmen, S.Kom., M.Si</h4>
                                                        <h5 class="mt-2">Deputi Bidang Sistem Informasi dan Digitalisasi Manajemen ASN</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-6">
                                                    <div class="team-box no-hover">
                                                        <div class="image">
                                                            <img src="/assets/images/tauchid.png" alt=""
                                                                style="width: 90%;" class="ms-2">
                                                        </div>
                                                        <div class="team-content gradient_bg">
                                                            <h4>Tauchid Djatmiko, SH, M.Si</h4>
                                                            <h5 class="mt-2">Analis SDM Aparatur Ahli Utama</h5>
                                                            <p>Badan Kepegawaian Negara</p>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div v-show="activeTab === 'pusat'" class="tab-pane fade show active" id="pusat">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="card border-0 shadow">
                                <div class="card-body">
                                    <hr>

                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-8 offset-md-2 col-sm-12 text-center">
                                                <div class="heading-title">
                                                    <h2 class="darkcolor bottom20">Dewan Pembina</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/yomo.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Drs. Haryomo Dwi Putranto, M.Hum.</h4>
                                                        <h5>Deputi Bidang Pembinaan Manajemen Kepegawaian</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/otok.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Dr. Otok Kuswandaru, S.Sos., M.Si., CIPA
                                                        </h4>
                                                        <h5>Deputi Bidang Pengawasan dan Pengendalian</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row justify-content-center padding_m">
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/kapusbin.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Dr. Achmad Slamet Hidayat, S.Pd, M.Si</h4>
                                                        <h5>kepala Pusat Pembinaan Jabatan Fungsional Kepegawaian</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/bayu.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Bajoe Leodi Hargono, M.M., M.T., M.Sc.
                                                        </h4>
                                                        <h5>Kepala Pusat Penilaian Kompetensi ASN</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/satya.png" alt="">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Satya Pratama</h4>
                                                        <h5>Kepala Pusat Pengembangan Kompetensi ASN</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="team-box no-hover">
                                                    <div class="image">
                                                        <img src="/assets/images/tauchid.png" alt="" style="width: 90%;"
                                                            class="ms-2">
                                                    </div>
                                                    <div class="team-content gradient_bg">
                                                        <h4>Tauchid Djatmiko, SH, M.Si</h4>
                                                        <h5>Analis SDM Aparatur Ahli Utama</h5>
                                                        <p>Badan Kepegawaian Negara</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->


                <div v-show="activeTab === 'pengawas'" class="tab-pane fade show active" id="pengawas">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="card border-0 shadow">
                                <div class="card-body">
                                    <hr>
                                    <section id="our-team-four">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-md-8 offset-md-2 col-sm-12 text-center">
                                                    <div class="heading-title">
                                                        <h2 class="darkcolor bottom20">Dewan Pengawas</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center">
                                                <div class="col-lg-3 col-md-6">
                                                    <div class="team-box no-hover">
                                                        <div class="image">
                                                            <img src="/assets/images/srigantini.png" alt="">
                                                        </div>
                                                        <div class="team-content gradient_bg">
                                                            <h4>Sri Gantini, S.Sos., M.A.P.
                                                            </h4>
                                                            <h5 class="mt-2">Direktur Jabatan Fungsional Manajemen ASN</h5>
                                                            <p>Badan Kepegawaian Negara</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div v-show="activeTab === 'cabang'" class="tab-pane fade show active" id="cabang">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="card border-0 shadow">
                                <div class="card-body">
                                    <h5 class="mt-2 text-center"><i class="fa fa-chart-line"></i> Pengurus Cabang</h5>
                                    <hr>
                                </div>
                            </div>
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
//import reactive
// (removed unused import)

//import inertia adapter
// (removed unused import)
//import Head from Inertia
import {
    Head
} from '@inertiajs/vue3';



export default {

    //layout
    layout: LayoutWebsite,

    //register component
    components: {
        Head,

    },

    //props
    props: {
        title: Object

    },

    data() {
        return {
            activeTab: 'pusat', // Set the default active tab

        };
    },

    methods: {
        setActiveTab(tabName) {
            this.activeTab = tabName;
        },
    },



}

</script>
