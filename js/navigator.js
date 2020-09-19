// var NavigationCache = new Array();
// $(document).ready(function(){
//   NavigationCache[window.location.pathname] = $('#ajax-container').html();
//   history.pushState({page: window.location.pathname, type: "page"}, document.title, window.location.pathname);
// });

function setPage(page, navString) {
  $.post(page, { ajaxLoad: true }, function(data){
    $('#ajax-container').html(data); 
    // NavigationCache[page] = data;
    if (navString)
    {
      history.pushState({page: page, type: "page"}, document.title, page);
    }
  })  
}

$(document).ready(function(){
  if (history.pushState) {
    window.onpopstate = function(event) {
      console.log('back = '+event.state.page);
      setPage(event.state.page);
      // if (event.state.type.length > 0) {
        // if (NavigationCache[event.state.page].length>0) {
          // $('#ajax-container').html(NavigationCache[event.state.page]);
        // }
      // }
    } 

    $('#ajax-container').on("click", '.navigation', function(){
        if ($(this).attr('href'))
        {
          setPage($(this).attr('href'), true);
          console.log($(this).attr('href'));
        }
        return false;
    })
    $('#ajax-container').on("click", '.silent', function(){
        if ($(this).attr('href'))
        {
          setPage($(this).attr('href'), false);
          console.log($(this).attr('href'));
        }
        return false;
    })
    $('#ajax-container').on('submit', '.forms', function(e){
        e.preventDefault();
        if ($(this).attr('action'))
        {
          // setPage($(this).attr('href'), false);
          console.log($(this).attr('action'));
          var msg = $(this).serialize();
          console.log(msg);
          $.ajax({
          type: 'POST',
          url: $(this).attr('action'),
          data: msg,
          success: function(data) {
            $('#ajax-container').html(data);
          },
          error:  function(xhr, str){
          alert('Возникла ошибка: ' + xhr.responseCode);
          }
          });
        }
        return false;
    })
  }

})
