<html lang="">
 <head>
  <title></title>
  <link rel="stylesheet" media="screen" href="https://mssante.appliops.fr/ui/css/style.css" type="text/css"/>
  <link rel="shortcut icon" href="https://mssante.appliops.fr/ui/img/favicon.ico" type="image/x-icon">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" media="screen" href="https://mssante.appliops.fr/ui/css/foundation.css" type="text/css"/>
  <meta charset="UTF-8"/>
 </head>
 <body>
  <fieldset style="border:1px solid #000000;">
   <legend>Bienvenue sur votre M&eacute;ssagerie Mssant&eacute;</legend>
   <div class="row">
    <div class="sidebar columns large-1 medium-1 small-12" style="border:1px solid red;">
       <ul>
          <?php foreach (($dossiers_mail?:[]) as $ikey=>$mail_folder): ?>
             <!-- <li><a href="/main/<?= ($ikey) ?>" ><?= (trim($mail_folder)) ?> </a></li> -->  
             <li>
              <!-- <form action="/main/<?= ($ikey) ?>" method="post" name="box<?= ($ikey) ?>" id="box<?= ($ikey) ?>"> -->
              <form action="/main" method="post" name="box<?= ($ikey) ?>" id="box<?= ($ikey) ?>">
               <input name="submit" value="<?= (trim($mail_folder)) ?>" type="submit" id="submit_box<?= ($ikey) ?>"/> 
               <input id="box_id_<?= ($ikey) ?>" name="box_id_<?= ($ikey) ?>" type="hidden" value="<?= ($ikey) ?>">
              </form>
             </li>
          <?php endforeach; ?>
       </ul>


    </div>
    <div class="menu_content columns large-11 medium-12 small-12" style="border:1px solid green;">
      <div class="row" style="border:1px solid red;border-radius: 7px;height: 60px;">
        <div class="nbmail columns large-2 medium-2 small-12" style="display: inline-block;border:1px solid #000000;box-shadow: 5px 8px 10px black;height: 100%;line-height: 3.65em;">
           nb mails
        </div>
        <div class="mode columns large-2 medium-2 small-12" style="display: inline-block;border:1px solid #000000;box-shadow: 5px 8px 10px black;height: 100%;line-height: 3.65em;">
           mode
        </div>
        <div class="username columns large-3 medium-2 small-12" style="display: inline-block;border:1px solid #000000;box-shadow: 5px 8px 10px black;height: 100%;line-height: 3.65em;">
           <?= ($id_utilisateur)."
" ?>
        </div>
        <div class="writedown columns large-2 medium-2 small-12" style="display: inline-block;border:1px solid #000000;box-shadow: 5px 8px 10px black;height: 100%;line-height: 3.65em;">
           &eacute;crire
        </div>
        <div class="logout columns large-2 medium-2 small-12" style="display: inline-block;border:1px solid #000000;box-shadow: 5px 8px 10px black;height: 100%;line-height: 3.65em;">

           <a href="/?logout=1"> d&eacute;connexion</a>

        </div>
      </div>

      <div style="border:1px solid green;padding: 23px;overflow: visible;">
         <?= ($mail_bd)."
" ?>
        <div class="mail-content-table">
       <?= (html_entity_decode($te))."
" ?>

          <?php foreach (($mails_header?:[]) as $ikey=>$mail_header): ?>
             <li class='row row-mail-header' style='list-style: none;padding: 0;margin:0;border-bottom: 1px solid black;'><a href="/main/<?= ($ikey) ?>" ><?= (html_entity_decode($mail_header)) ?> </a></li>
          <?php endforeach; ?>

        </div>
      </div>
    </div>













   </div>
  </fieldset>
 </body>

</html>
