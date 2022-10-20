<?php 

$options = get_option( 'cf7du_options' );

$border_color = $options['cf7du_border_color']?$options['cf7du_border_color']:"#999999";

$border_hover_color = $options['cf7du_border_hover_color']?$options['cf7du_border_hover_color']:"#4A90E2";

$border_thickness = $options['cf7du_border_thickness']?(int)$options['cf7du_border_thickness']:"3";

$title_color = $options['cf7du_title_color']?$options['cf7du_title_color']:"#9B9B9B";

$icon_color = $options['cf7du_icon_color']?$options['cf7du_icon_color']:"#9B9B9B";

?>
.drop_uploader.drop_zone,
div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone {
    width: 100%;
    min-height: 100px;
    text-align: center;
    border: <?php echo $border_thickness; ?>px dashed <?php echo $border_color; ?>;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    border-radius: 8px;
    margin: 10px 0;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone.hover {
    border: <?php echo $border_thickness; ?>px dashed <?php echo $border_hover_color; ?>;
}

.drop_uploader.drop_zone .text_wrapper {
    margin-top: 10px;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone .text_wrapper {
    
}

.drop_uploader.drop_zone .text_wrapper i,
div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone .text_wrapper i {
    font-size: 50px;
    color: <?php echo $icon_color; ?>;
    position: relative;
    top: 14px;
    margin-right: 25px;
}

.drop_uploader.drop_zone .text,
div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone .text {
    font-family: inherit;
    font-size: 24px;
    color: <?php echo $title_color; ?>;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files {
    width: 90%;
    text-align: left;
    list-style: none;
    margin: 10px auto 35px !important;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files li {
    font-family: inherit;
    font-size: 18px;
    color: #000000;
    background-color: #f5f5f5;
    border-top: 2px solid #f5f5f5;
    border-bottom: 2px solid #f5f5f5;
    margin: 0;
    padding: 5px;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files li i.action-delete {
    color: #ff0000;
    float: right;
    cursor: pointer;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb {
    width: 90%;
    text-align: left;
    list-style: none;
    margin-bottom: 35px;
    overflow: auto;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li {
    min-width: 100px;
    display: block;
    float: left;
    font-family: inherit;
    font-size: 18px;
    color: #000000;
    background-color: transparent;
    border: none;
    margin: 0 !important;
    padding: 5px 15px !important;
    text-align: center;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail {
    width: 100px;
    height: 100px;
    border-radius: 8px 8px 8px 8px;
    -moz-border-radius: 8px 8px 8px 8px;
    -webkit-border-radius: 8px 8px 8px 8px;
    background-size: cover;
    margin: 0;
    border: 1px solid <?php echo $icon_color; ?>;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail .du_hover_layer {
    width: 98px;
    height: 98px;
    border-radius: 7px;
    background: #fff;
    opacity: 0;
    position: absolute;
    -webkit-transition: opacity 0.1s ease-in;
    -moz-transition: opacity 0.1s ease-in;
    transition: opacity 0.1s ease-in;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail:hover .du_hover_layer {
    opacity: .8;
    position: absolute;
    -webkit-transition: opacity 0.1s ease-in;
    -moz-transition: opacity 0.1s ease-in;
    transition: opacity 0.1s ease-in;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files li .du_progress {
    display: inline-block;
    float: right;
    margin: 4px 0px;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li .du_progress {
    width: 0px;
    height: 0px;
    margin-top: -100px;
    position: relative;
    right: 75px;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li .du_progress canvas {
    margin-top: 25px;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail i {
    font-size: 32px;
    position: relative;
    top: calc(50% - 16px);
    display: block;
    color: <?php echo $border_color; ?>;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail.rotate_90 {
    -webkit-transform: rotate(-90deg);
    -moz-transform: rotate(-90deg);
    -ms-transform: rotate(-90deg);
    -o-transform: rotate(-90deg);
    transform: rotate(-90deg);
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail.rotate_180 {
    -webkit-transform: rotate(-180deg);
    -moz-transform: rotate(-180deg);
    -ms-transform: rotate(-180deg);
    -o-transform: rotate(-180deg);
    transform: rotate(-180deg);
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail.rotate_270 {
    -webkit-transform: rotate(90deg);
    -moz-transform: rotate(90deg);
    -ms-transform: rotate(90deg);
    -o-transform: rotate(90deg);
    transform: rotate(90deg);
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb.ajax li div.thumbnail:hover i {
    visibility: hidden;
    opacity: 0;
    display: none;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail i.action-delete {
    color: #FF0000;
    visibility: hidden;
    opacity: 0;
    float: none;
    -webkit-transition: opacity 0.1s ease-in;
    -moz-transition: opacity 0.1s ease-in;
    transition: opacity 0.1s ease-in;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li div.thumbnail:hover i.action-delete {
    visibility: visible;
    opacity: 1;
    display: block;
    cursor: pointer;
    -webkit-transition: opacity 0.1s ease-in;
    -moz-transition: opacity 0.1s ease-in;
    transition: opacity 0.1s ease-in;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files.thumb li span.title {
    width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files li:nth-child(even) {
    background-color: transparent;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone ul.files li i {
    font-size: 20px;
    position: relative;
    top: 2px;
    margin: 0px 10px;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone .errors p {
    color: #FF0000;
}

div.wpforms-container-full .wpforms-form div.drop_uploader.drop_zone input[type=file] {
    display: none;
    opacity: 0;
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
}