   <!-- Begin Page Content -->
   <div class="container-fluid">

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800"><?=$title; ?></h1>

</div class="row">
    <div class="col-lg-6">
         <!-- pesan error -->
         <?= form_error(
             'menu',
             '<div class="alert alert-success" role="alert">
             </div>'
         ); ?>
         <?= $this->session->flashdata('message'); ?>
         <!-- akhir pesan eror -->

         <!-- tombol tambah -->
         <a href="" class="btn btn-primary mb-3" class="btn btn-primary"
         data-toggle="modal" data-target="#logoutModal">Add Menu</a>
         <!-- Tabel -->
         <table class="table table-hover">
             <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Menu</th>
                    <th scope="col">Action</th>

                </tr>
            <thead>
            <tbody>
                 <?php $i = 1; ?>
                 <?php foreach ($menu as $m)  : ?>
                     <tr>
                        <th scope="row"><?= $i; ?></th>
                        <td ><?= $m["menu"]; ?></td>      
                        <td>
                             <button href="#" class="btn btn-success" data-toggle="modal" data-popup="tooltip" data-placement="top" title=
                             "edit" data-target="#exampleModalmenuedit<?= $m['id']; ?>">edit</button>
                             <button onclick="hapusMenu('<?= base_url('menu/hapusmenu/') . $m['id'] ?>')"
                             class="btn btn-danger tombol-hapus">Delete</button>
                        </td>
                    <tr>  
                <?php $i++; ?>
                <?php endforeach; ?>  
            <tbody> 
        <table>
        <!-- akhir tabel -->    

    </div>
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Button trigger modal-->
 <!-- Modal -->
 <div class="modal fade" id="logoutModal" tabindex="-1"
 aria-labelledby="newMenuModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="newModalLabel">Add new Menu</h5>
                   <button type="button" class="btn-close"
                   data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <form action="<?= base_url('menu'); ?>" method="post">
                   <div class="modal-body">
                        <div class="form-group">
                             <input type="text" class="form-control" id="menu"
                             name="menu" placeholder="Menu Name">
                        </div>
                   </div>  

                   <div class ="modal-footer">
                       <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                       <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                 </form>
            </div>   
        </div>
    </div>    
</div>


<!-- Modal Edit-->
<?php foreach ($menu as $m) : ?>
<div class="modal fade" id="exampleModalmenuedit<?= $m['id']; ?>">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="newModalLabel">Edit Menu</h5>
                   <button type="button" class="btn-close"
                   data-bs-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                   </button>
               </div>
               <form action="<?= base_url('menu/editmenu'); ?>" method="post">
               
                 
                   <div class="modal-body">
                        <div class="form-group">
                             <input type="text" class="form-control" id="menu"
                             name="menu" placeholder="Menu Name" value="<?= $m['menu'] ?>">
                        </div>

                        <div>
                        <input type="hidden" class="form-control"
                   readonly value="<?= $m['id']; ?>" name="id">
                    </div>
                   </div>  

                   <div class ="modal-footer">
                       <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>
                       <button type="submit" class="btn btn-primary"></i>Edit</button>
                    </div>
                 </form>
            </div>   
        </div>
    </div>    
<?php endforeach; ?>
