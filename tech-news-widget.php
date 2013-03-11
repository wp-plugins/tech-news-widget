<?php
/*
Plugin Name: Tech News Widget
Plugin URI: http://www.naee.pk
Description: Adds a customizeable widget which displays the latest news from www.emoiz.com
Version: 1.0
Author: Naeem
Author http://www.naeem.pk/
License: GPL3
*/

function emoiz()
{
  $options = get_option("widget_emoiz");
  if (!is_array($options)){
    $options = array(
      'title' => 'EMOIZ News',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen
  $rss = simplexml_load_file(
  'http://www.emoiz.com/feed');
  ?>

  <ul>

  <?php
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];

  // RSS Elemente durchlaufen
  $cnt = 0;
  foreach($rss->channel->item as $i) {
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?>

    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a>
    </li>

    <?php
    $cnt++;
  }
  ?>

  </ul>
<?php
}

function widget_emoiz($args)
{
  extract($args);

  $options = get_option("widget_emoiz");
  if (!is_array($options)){
    $options = array(
      'title' => 'Tech News',
      'news' => '5',
      'chars' => '30'
    );
  }

  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  emoiz();
  echo $after_widget;
}

function emoiz_control()
{
  $options = get_option("widget_emoiz");
  if (!is_array($options)){
    $options = array(
      'title' => 'Tech News',
      'news' => '5',
      'chars' => '30'
    );
  }

  if($_POST['emoiz-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['emoiz-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['emoiz-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['emoiz-CharCount']);
    update_option("widget_emoiz", $options);
  }
?>
  <p>
    <label for="emoiz-WidgetTitle">Widget Title: </label>
    <input type="text" id="emoiz-WidgetTitle" name="emoiz-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="emoiz-NewsCount">Max. News: </label>
    <input type="text" id="emoiz-NewsCount" name="emoiz-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="emoiz-CharCount">Max. Characters: </label>
    <input type="text" id="emoiz-CharCount" name="emoiz-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="emoiz-Submit"  name="emoiz-Submit" value="1" />
  </p>

<?php
}

function emoiz_init()
{
  register_sidebar_widget(__('Tech News'), 'widget_emoiz');
  register_widget_control('Tech News', 'emoiz_control', 300, 200);
}
add_action("plugins_loaded", "emoiz_init");
?>