<form id="frm-cc" action="/index.php">
    <input type="hidden" name="type"  value="constantcontact"/>
    <input type="hidden" name="access_token" id="cc_access_token" value=""/> 
</form>
<script type="text/javascript">
    var url = location.hash;
    if (url.indexOf("#access_token") > -1) {
        var new_url = url.replace("#", "");
        console.log(new_url);
        var segments = new_url.split("&");
        var access_token = "";
        console.log(segments);
        if (segments.length > 0) {
            access_token = segments[0].split("=");
        }
        document.getElementById("cc_access_token").value = access_token[1];
        document.getElementById("frm-cc").submit();
    }
</script>