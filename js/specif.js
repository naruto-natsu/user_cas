/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function groupsEnabled() {
    alert("ACTION");
}

alert("ACTION");

$(document).ready(function() {
        $('#casGroupsEnabled').change( function() {
            alert('ACTION');
        if ($(this).val() !== "1") {
            $('#testLabel').hide();
        }
        else {
            $('#testLabel').show();
        }
    });
});    