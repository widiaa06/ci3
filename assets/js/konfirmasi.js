function hapusMenu(url) {
    swal.fire({
        title: 'Are youn sure',
        text:  "Yakin ingin hapus menu",
        icon:  'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya,hapus!'
    }).then((result) => {
        if(result.value) {
            document.location.href = url;
        }
    })
}


function hapusRole(url) {
    swal.fire({
        title: 'Are youn sure',
        text:  "Yakin ingin hapus role",
        icon:  'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya,hapus!'
    }).then((result) => {
        if(result.value) {
            document.location.href = url;
        }
    })
}