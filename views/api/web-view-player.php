<style>
    body{
        margin: 0;
        background-color: #fafafa;
        font-size: 16px !important;
    }
    body p {
        font-size: 16px !important;
    }
    .container{
        padding: 0;
    }
    .body{
        padding: 10px 20px;
        background-color: #efefef;
    }
    .block{
        background-color: #fff;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0px 0px 10px 1px rgba(0,0,0,0.1);
    }
    audio{
        width: 100%;
        margin: 10px 0;
    }
    .mejs__container{
        max-width: 100%;
        margin: 10px 0;
    }
</style>
<audio controls>
    <source src="/<?= $model->file_src; ?>" type="audio/mpeg">
</audio>
<script
  src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
  integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
  crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.12/mediaelement-and-player.min.js" integrity="sha256-z7JbZVaNbNzLvOCFHUNrjqnZRojZbRAxgr4KU2qL0qc=" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.12/mediaelementplayer.min.css" integrity="sha256-ji1bfJaTGnyscoc7LzcV9yNJy5vGKJ0frO3KJo1oaGQ=" crossorigin="anonymous" />
  <script>
    $(document).ready(function(){
        $('video, audio').mediaelementplayer({
            // Do not forget to put a final slash (/)
            pluginPath: 'https://cdnjs.com/libraries/mediaelement/',
            // this will allow the CDN to use Flash without restrictions
            // (by default, this is set as `sameDomain`)
            shimScriptAccess: 'always'
            // more configuration
        });
    })
  </script>