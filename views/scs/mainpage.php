<style>
body {
  background: #eaecfa;
}

.loader {
  width: 250px;
  height: 50px;
  line-height: 50px;
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
          transform: translate(-50%, -50%);
  font-family: helvetica, arial, sans-serif;
  text-transform: uppercase;
  font-weight: 900;
  color: #ce4233;
  letter-spacing: 0.2em;
}
.loader::before, .loader::after {
  content: "";
  display: block;
  width: 15px;
  height: 15px;
  background: #ce4233;
  position: absolute;
  -webkit-animation: load .7s infinite alternate ease-in-out;
          animation: load .7s infinite alternate ease-in-out;
}
.loader::before {
  top: 0;
}
.loader::after {
  bottom: 0;
}

@-webkit-keyframes load {
  0% {
    left: 0;
    height: 30px;
    width: 15px;
  }
  50% {
    height: 8px;
    width: 40px;
  }
  100% {
    left: 235px;
    height: 30px;
    width: 15px;
  }
}

@keyframes load {
  0% {
    left: 0;
    height: 30px;
    width: 15px;
  }
  50% {
    height: 8px;
    width: 40px;
  }
  100% {
    left: 235px;
    height: 30px;
    width: 15px;
  }
}
#main-content{
    /* display: none; */
}
.date{
    margin: 10px;
    text-align: center;
    padding: 10px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0px 0px 10px 1px rgba(0,0,0,0.2);
}
.date .sep{
    display: none;
}
blockquote {
    margin: 0;
}
.list{
    background: #fff;
    border-radius: 10px;
    box-shadow: 0px 0px 10px 1px rgba(0,0,0,0.2);
    margin: 10px;
    padding: 20px;
    margin-bottom: 30px;
}
.sep {
    display: block;
    height: 1px;
    width: 100%;
    background: #d6cece;
    margin-top: 20px;
    margin-bottom: 20px;
}
a {
    color: tomato;
}
</style>

<div style="position: absolute; left: 0; top:0;" id="main-content"><?= $html; ?></div>
<!-- <div style="
    position: absolute;
    left: 0;
    top: 0;
    z-index: 2;
    width: 100%;
    height: 100%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
        -ms-flex-pack: center;
            justify-content: center;
    -webkit-box-align: center;
        -ms-flex-align: center;
            align-items: center;
    "
    >
    <div id="loader" class="loader">Loading...</div>
</div> -->
<script
  src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
  integrity="sha256-3edrmyuQ0w65f8gfBsqowzjJe2iM6n0nKciPUp8y+7E="
  crossorigin="anonymous"></script>
<script>
// function getPreviousSiblings(elem, filter) {
//     var sibs = [];
//     while ((elem = elem.previousSibling)) {
//         if (elem.nodeType === 3) continue; // ignore text nodes
//         if (!filter || filter(elem)) sibs.push(elem);
//     }
//     return sibs;
// }
// var prevs = getPreviousSiblings(document.getElementsByClassName('m1_grey')[0]);
// prevs.forEach(function (el) {
//     el.parentNode.removeChild(el);
// })
// document.getElementsByClassName('m1_grey')[0].parentNode.removeChild(document.getElementsByClassName('m1_grey')[0]);
// $("blockquote.list").last().nextAll().remove();

// console.log(prevs);

$(document).ready(function(){
    $(".m1_grey").prevAll().remove();
    $(".m1_grey").remove();
    $("blockquote.list").last().nextAll().remove();
      // $("img").each(function (index, elem) {
      //     img_attr = 'http://scsmath.com/' + $(elem).attr('src');
      //     $(elem).attr('src', img_attr);
      // });
    $("#main-content").find('hr:first').remove();
    $("blockquote.list").each(function (index, elem) {
        if ($(elem).prev().prop('tagName') == 'P') {
            $(elem).prev().addClass('date');
            if ($(elem).find('img').length == 0) {
                $(elem).addClass('remove');
            }
            // $(elem).remove();
        }
    });
    $(".remove").remove();
    $('img[src="http://scsmath.com/grfx/trnsprnt.gif"]').each(function(index, elem){
        if ($(elem).prev().attr('clear') == 'all') {
            $(elem).addClass('sep');
        }
    });
    $('a').each(function(index, elem){
      if ($(elem).attr('href').split('/')[0] == 'audio') {
        $(elem).attr('href', 'http://scsmath.com/'+$(elem).attr('href'));
      }
      
    });
//     setTimeout(function(){
//         $("#loader").fadeOut('300');
//         $("#main-content").fadeIn('300');
//         setTimeout(function(){
//             $("#loader").parent().remove();
//         }, 300)    
//     }, 1000);
});
</script>
