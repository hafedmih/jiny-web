/**
 * @author Batch Themes Ltd.
 */
// (function() {
//     'use strict';

//     $(function() {

//         var config = {
//             name: 'DDA',
//             theme: 'palette-6',
//             palette: getPalette('palette-6'),
//             layout: 'default-sidebar',
//             direction: 'ltr', //ltr or rtl
//             colors: getColors()
//         };


//         // $.removeAllStorages();
//         // $.localStorage.set('config', config);

//         var el = $('.main');
//         var wh = $(window).height();
//         el.css('min-height', wh + 'px');

//         var el2 = $('.main-view');
//         el2.css('min-height', (wh - 54) + 'px');

//         $('[data-toggle="tooltip"]').tooltip();

//     });
// })();





function deleteAction(slug, actionUrl){
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            window.location.href = actionUrl;
            swal("Poof! Your data has been deleted!", {
                icon: "success",
            });
          
        } else {
          swal("Your Data is safe!");
        }
      });

    return false;
}
function activeAction(actionUrl){
    swal({
        title: "Are you sure?",
        text: "You will change status for this data",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            window.location.href = actionUrl;
            swal("Poof! Your data status has been changed!", {
                icon: "success",
            });
          
        } else {
          swal("Your Data status is not chenged!");
        }
      });

    return false;
}
function activeActionstatus(actionUrl){
  swal({
      title: "Are you sure?",
      text: "You will change status for this data",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
          window.location.href = actionUrl;
          
        
      } else {
        swal("Your Data status is not chenged!");
      }
    });

  return false;
}

function banAction(slug, actionUrl){
  swal({
    title: "Are you sure?",
    text: "Do you want to block this User",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
        window.location.href = actionUrl;
        swal("Poof! User is blocked!", {
            icon: "success",
        });
      
    } else {
      swal("Your user is safe!");
    }
  });
  return false;
}
function unbanAction(slug, actionUrl){
  swal({
    title: "Are you sure?",
    text: "Do you want to unblock this User",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
        window.location.href = actionUrl;
        swal("Yes! User is unblocked!", {
            icon: "success",
        });
      
    } else {
      swal("Poof! Your user is still block!");
    }
  });
  return false;
}
function notification(title, text){
  new PNotify({
      title: title,
      text: text,
      addclass: 'bg-success border-success'
  });
}