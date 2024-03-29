<?php
  if ( ! defined( 'ABSPATH' ) ) {
     exit;
 } 
if(!empty($search)){
 ?>
<p class="vx_table_actions">
<input type="search" placeholder="<?php _e('Search', 'contact-form-entries'); ?>"  data-column="all" class="vx_search">
<?php
if(!empty($export)){ ?><a class="vx_export_btn" href="?vx_crm_form_action=download_csv&vx_crm_key=<?php echo esc_html($export) ?>"><?php _e('Download CSV', 'contact-form-entries'); ?></a> <?php } ?>
 </p>
<?php
} 
$upload=vxcf_form::get_upload_dir();
   $thumb_fields=array();   
     if(!empty($atts['thumb-cols'])){
     $thumb_fields=array_map('trim',array_map('strtolower',explode(',',$atts['thumb-cols'])));  
     } 
  $thumb_width='70';  if(!empty($atts['thumb-width'])){ $thumb_width=$atts['thumb-width']; } 
?>
 <table <?php echo $class.' '.$css ?> cellspacing="0" <?php echo wp_kses_post($table_id) ?>>
  <thead>
  <tr>
  <?php
      if(!empty($atts['number-col'])){
  ?>
<th <?php if(!empty($atts['number-col-width'])){ echo 'style="width:'.esc_html($atts['number-col-width']).' "'; } ?>><?php _e('#','contact-form-entries'); ?></th>
<?php
      }
$total_cols=2;
  foreach($fields as $field){  
$total_cols++;
?>
  <th><?php echo esc_html($field['label']) ?></th>
<?php
  }
?>

  </tr>
  </thead>
  
  <tfoot>
  <tr>
  <?php
      if(!empty($atts['number-col'])){
  ?>
<th <?php if(!empty($atts['number-col-width'])){ echo 'style="width:'.esc_html($atts['number-col-width']).' "'; } ?>><?php _e('#','contact-form-entries'); ?></th>
<?php
      }
  foreach($fields as $field){  
?>
  <th><?php echo esc_html($field['label']) ?></th>
<?php
  }
?>

  </tr>
  
  </tfoot>
  <tbody>
  <?php
  if(is_array($leads) && !empty($leads)){
  $sno=0;
      foreach($leads as $lead){
  $sno++;
  ?>
  <tr>
  <?php
      if(!empty($atts['number-col'])){
  ?>
  <td><?php echo esc_html($sno) ?></td>
    <?php
      }
foreach($fields as $field){
$field_name=$field['name'].'_field';  
if($field['name'] == 'time'){
  $field['name']='created';  
}
if(isset($field['label'] ) && in_array( strtolower($field['label']),$thumb_fields)){
$field['type']='thumb';          
}
$field_label='';
if(isset($lead['detail'][$field_name])){
 $field_label=maybe_unserialize($lead['detail'][$field_name]);   

 }
if($field['name'] == 'created' && !empty($lead['created'])){
   $field_label=strtotime($lead['created']); //+$offset
$field_label= date('M-d-Y H:i:s',$field_label);   
}
if(isset($field['type']) && in_array($field['type'],array('file','thumb')) && !empty($field_label) && self::$is_pr){ 
    if(!is_array($field_label)){
        $field_label=array($field_label);
    }
 $file_urls=array();   
foreach($field_label as $file_url){
    $file_arr=explode('/',$file_url);
$file_name=$file_arr[count($file_arr)-1];

    if(filter_var($file_url,FILTER_VALIDATE_URL) === false){  
  $field_label=$upload['url'].$file_url;     
    } 
   // echo $field_label;
 if($field['type'] == 'thumb'){   
$file_urls[]='<div class="vxcf_front_thumb" style="max-width:'.esc_attr($thumb_width).'px "> <img src="'.$field_label.'"></div>';    
 }else{ 
$file_urls[]='<a href="'.esc_url($field_label).'" target="_blank" class="vxcf_front_file_url">'.esc_attr($file_name)."</a>";     
} }
$field_label=implode("<br/>",$file_urls);
}
if(is_array($field_label)){
  $field_label=implode(', ',$field_label);  
}
?>
<td><?php echo wp_kses_post($field_label); ?></td>
  <?php
  }
  ?>

  </tr>
  <?php
  }
  }
  else {  
  ?>
  <tr>
    <td colspan="<?php echo esc_html($total_cols) ?>">
        <?php _e("No Record(s) Found", 'contact-form-entries'); ?>
    </td>
  </tr>
  <?php
  }
  ?>
  </tbody>
  </table>
  <?php
   if(!empty($atts['sortable'])){
       if(!empty($atts['pager']) && $page_size<count($leads)){
  ?>
 <div class="vx_pager">
  <form>
    <img src="<?php echo esc_url($base_url) ?>images/first.png" class="vx_first"/>
    <img src="<?php echo esc_url($base_url) ?>images/prev.png" class="vx_prev"/>
    <span class="vx_pagedisplay"></span> <!-- this can be any element, including an input -->
    <img src="<?php echo esc_url($base_url) ?>images/next.png" class="vx_next"/>
    <img src="<?php echo esc_url($base_url) ?>images/last.png" class="vx_last"/>
    <?php
        $rows=count($leads)/$page_size;
        $pages_count=floor($rows);
        if($pages_count>0){
            ?>
    <select class="vx_pagesize">
    <?php
        for($i=1; $i<$pages_count; $i++ ){
    $op=$page_size*$i;
            ?>
      <option value="<?php echo esc_html($op) ?>"><?php echo esc_html($op) ?></option>
<?php
        }
?>
      <option value="all"><?php _e('All Rows', 'contact-form-entries'); ?></option>
    </select>
    <?php
        }
    ?>
  </form>
</div>
<?php
       }
       
       ?>
    <script>
    jQuery(function($){
        $pager = $('.vx_pager');
        $table=$('.vx_entries_table');

        $table.tablesorter({
       
          //  theme : 'blue',
   <?php
   if(!empty($search)){
   ?> 
       widgets        : ['filter'],     
    widgetOptions : {
       filter_external : '.vx_search',
      filter_columnFilters: false,
      filter_searchDelay : 100,
      filter_hideEmpty : true,
    },
    <?php } ?>
       usNumberFormat : false,
            sortReset      : true,
            sortRestart    : true
        });
        
   /// $.tablesorter.filter.bindSearch( $table, $('#vx_search') );   
       <?php
   if(!empty($atts['pager'])){
  ?>   
        $table.tablesorterPager({
      // target the pager markup - see the HTML block below
      container: $pager,
      size: <?php echo (int)esc_html($page_size) ?>,
         // css class names of pager arrows
    cssNext: '.vx_next', // next page arrow
    cssPrev: '.vx_prev', // previous page arrow
    cssFirst: '.vx_first', // go to first page arrow
    cssLast: '.vx_last', // go to last page arrow
 //   cssGoto: '.gotoPage', // select dropdown to allow choosing a page

    cssPageDisplay: '.vx_pagedisplay', // location of where the "output" is displayed
    cssPageSize: '.vx_pagesize', // page size selector - select dropdown that sets the "size" option
      output: 'showing: {startRow} to {endRow} ({filteredRows})'
    });
    <?php
   }
    ?>

    //////
    });
    </script>
<?php
        }
     ?>  
  <style type="text/css">
  .vxcf_front_thumb img{
      width:100%;
      display: block;
  }
  .vx_export_btn{
      float: right;
  }
/* pager wrapper, div */
.vx_pager {
  padding: 5px;
}
.vx_entries_table{
    display: block;
    overflow: auto;
}
/* pager navigation arrows */
.vx_pager img {
  vertical-align: middle;
  margin-right: 2px;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: inline-block;
  border-width: 0;
  padding: 0;
}
.vx_pager img.disabled {opacity: .5; cursor: default;}
.vx_paging_icon{
    font-size: 26px;
    vertical-align: middle;
    margin: 0 4px 0 2px;
    cursor: pointer;
}
.vx_paging_icon.disabled{color: #ccc; cursor: default;}

/* pager output text */
.vx_pager .pagedisplay {
  padding: 0 5px 0 5px;
  width: 50px;
  text-align: center;
}

/* pager element reset (needed for bootstrap) */
.vx_pager select {
  margin: 0;
  padding: 0;
}
.tablesorter .filtered {
  display: none;
}
.tablesorter-default .header,
.tablesorter-default .tablesorter-header {
    background-image: url(data:image/gif;base64,R0lGODlhFQAJAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAkAAAIXjI+AywnaYnhUMoqt3gZXPmVg94yJVQAAOw==);
    background-position: center right;
    background-repeat: no-repeat;
    cursor: pointer;
    white-space: normal;
}
.tablesorter-default thead .headerSortUp,
.tablesorter-default thead .tablesorter-headerSortUp,
.tablesorter-default thead .tablesorter-headerAsc {
    background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjI8Bya2wnINUMopZAQA7);
    border-bottom: #000 2px solid;
}
.tablesorter-default thead .headerSortDown,
.tablesorter-default thead .tablesorter-headerSortDown,
.tablesorter-default thead .tablesorter-headerDesc {
    background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjB+gC+jP2ptn0WskLQA7);
    border-bottom: #000 2px solid;
}
/* tfoot */
.tablesorter-default tfoot .tablesorter-headerSortUp,
.tablesorter-default tfoot .tablesorter-headerSortDown,
.tablesorter-default tfoot .tablesorter-headerAsc,
.tablesorter-default tfoot .tablesorter-headerDesc {
    border-top: #000 2px solid;
}
/* table processing indicator */
.tablesorter-default .tablesorter-processing {
    background-position: center center !important;
    background-repeat: no-repeat !important;
    /* background-image: url(images/loading.gif) !important; */
    background-image: url('data:image/gif;base64,R0lGODlhFAAUAKEAAO7u7lpaWgAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQBCgACACwAAAAAFAAUAAACQZRvoIDtu1wLQUAlqKTVxqwhXIiBnDg6Y4eyx4lKW5XK7wrLeK3vbq8J2W4T4e1nMhpWrZCTt3xKZ8kgsggdJmUFACH5BAEKAAIALAcAAAALAAcAAAIUVB6ii7jajgCAuUmtovxtXnmdUAAAIfkEAQoAAgAsDQACAAcACwAAAhRUIpmHy/3gUVQAQO9NetuugCFWAAAh+QQBCgACACwNAAcABwALAAACE5QVcZjKbVo6ck2AF95m5/6BSwEAIfkEAQoAAgAsBwANAAsABwAAAhOUH3kr6QaAcSrGWe1VQl+mMUIBACH5BAEKAAIALAIADQALAAcAAAIUlICmh7ncTAgqijkruDiv7n2YUAAAIfkEAQoAAgAsAAAHAAcACwAAAhQUIGmHyedehIoqFXLKfPOAaZdWAAAh+QQFCgACACwAAAIABwALAAACFJQFcJiXb15zLYRl7cla8OtlGGgUADs=') !important;
}

/* optional disabled input styling */
.tablesorter-default .tablesorter-filter-row .disabled {
    opacity: 0.5;
    filter: alpha(opacity=50);
    cursor: not-allowed;
}


</style> 





