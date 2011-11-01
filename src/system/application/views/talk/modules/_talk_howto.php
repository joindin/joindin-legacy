<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="box">
    <h4>Speaker F.A.Q.</h4>
    <div class="ctn">
        <p>Congratulations! Your claim on this talk has been approved. Here are some helpful tips on frequently asked question:</p>
        <ul class="toggle-faq">
            <li>
                <a href="#" class="question">How do I add my slides?</a>
                <div class="answer">
                    You can add or edit a link to your slides by going to the
                    <a href="/talk/edit/<?php echo $detail->tid; ?>">edit page</a>
                    for this talk and entering the URL for the slides.
                </div>
            </li>
            <li>
                <a href="#" class="question">Where can I find more of my claimed talks?</a>
                <div class="answer">
                    You can see a listing of all your claimed talks by visiting
                    the <a href="/user/main">My Talks</a> section of your account
                    page.
                </div>
            </li>
            <li>
                <a href="#" class="question">Am I allowed to comment on my talk?</a>
                <div class="answer">
                    Of course! While you won't be allowed to give it a rating,
                    feel free to discuss with the others giving their comments
                    about your talk.
                </div>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.toggle-faq .question').each(function() {
        $(this).click(function() {
            $(this).next().each(function() {
                if ($(this).css('display') != 'none')
                {
                    $(this).slideUp('fast');
                }
                else
                {
                    $(this).slideDown('fast');
                }
            });
            
            return false;
        });
    });
});
</script>
