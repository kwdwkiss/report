<?php
$popWindowConfig = \App\Config::get('site.popWindowConfig');
$title = array_get($popWindowConfig, 'title');
$content = array_get($popWindowConfig, 'content');
?>
<div class="modal fade" id="pop-window" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">{{$title}}</h4>
            </div>
            <div class="modal-body">
                {{$content}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.onload = function () {

    }
</script>