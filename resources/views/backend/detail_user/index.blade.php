@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Detail User</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif


                    <form action="{{ url('store-detail-user') }}" method="POST">
                        @csrf
                        <div class="row">

                            <input type="hidden" name="id" value="{{ $data->id_user }}">

                            <!-- Email -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input name="email" id="email" type="email" placeholder="email"
                                        class="form-control form-control-sm" value="{{ $data->email ?? '' }}" required>
                                </div>
                            </div>

                            <!-- Nabil -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" id="name" type="text" placeholder="name"
                                        class="form-control form-control-sm" value="{{ $data->name ?? '' }}" required>
                                </div>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control form-control-sm">
                                        <option value="">-- pilih --</option>
                                        <option value="Laki-laki"
                                            {{ isset($data) && $data->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="Perempuan"
                                            {{ isset($data) && $data->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tempat Lahir -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Tempat Lahir</label>
                                    <input name="tempat_lahir" type="text" placeholder="Tempat lahir"
                                        class="form-control form-control-sm" value="{{ $data->tempat_lahir ?? '' }}">
                                </div>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input name="tanggal_lahir" type="date" class="form-control form-control-sm"
                                        value="{{ $data->tanggal_lahir ?? '' }}">
                                </div>
                            </div>

                            <!-- NIP -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>NIP</label>
                                    <input name="nip" type="text" placeholder="NIP"
                                        class="form-control form-control-sm" value="{{ $data->nip ?? '' }}">
                                </div>
                            </div>

                            <!-- Jenis ASN -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Jenis ASN</label>
                                    <select name="jenis_asn" class="form-control form-control-sm">
                                        <option value="">-- pilih --</option>
                                        <option value="PNS"
                                            {{ isset($data) && $data->jenis_asn == 'PNS' ? 'selected' : '' }}>PNS</option>
                                        <option value="PPPK"
                                            {{ isset($data) && $data->jenis_asn == 'PPPK' ? 'selected' : '' }}>PPPK
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Jabatan -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <input name="jabatan" type="text" placeholder="Jabatan"
                                        class="form-control form-control-sm" value="{{ $data->jabatan ?? '' }}">
                                </div>
                            </div>

                            <!-- Instansi Kerja -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Instansi Kerja</label>
                                    <input name="instansi_kerja" type="text" placeholder="Instansi Kerja"
                                        class="form-control form-control-sm" value="{{ $data->instansi_kerja ?? '' }}">
                                </div>
                            </div>

                            <!-- Satuan Kerja -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Satuan Kerja</label>
                                    <input name="satuan_kerja" type="text" placeholder="Satuan Kerja"
                                        class="form-control form-control-sm" value="{{ $data->satuan_kerja ?? '' }}">
                                </div>
                            </div>

                        </div>

                        <!-- Button -->
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
