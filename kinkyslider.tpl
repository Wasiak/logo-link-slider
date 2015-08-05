<!-- Module KinkySlider -->

<link href="{$base_dir}modules/kinkyslider/css/style.css" rel="stylesheet" type="text/css" media="all" />

<style type="text/css">

    #bazingaSliderContainer {

        border-color:   #{$kinkyslider_config.borderColor};
        border-width:   {$kinkyslider_config.borderWidth}px;
        border-style:   solid;        
        width:          {$kinkyslider_calculated_image_width}px;
        height:         {$kinkyslider_calculated_image_height}px;
        margin-top:     {$kinkyslider_config.marginTop};
        margin-right:   {$kinkyslider_config.marginRight};
        margin-bottom:  {$kinkyslider_config.marginBottom};
        margin-left:    {$kinkyslider_config.marginLeft};

    }

    ul#bazingaSliderContent li {

        width:          {$kinkyslider_calculated_image_width}px;
        height:         {$kinkyslider_calculated_image_height}px;

    }

    ul#bazingaSliderContent li .bazingaHeader {

        background:     #{$kinkyslider_config.headerBackground};
        bottom:         {$kinkyslider_config.headerMarginBottom}px;
        color:          #{$kinkyslider_config.headerFontColor};
        font-family:    {$kinkyslider_config.headerFont};
        font-size:      {$kinkyslider_config.headerFontSize}px;
        left:           {$kinkyslider_config.headerMarginLeft}px;
        line-height:    {$kinkyslider_config.headerLineHeight}px;
        padding-top:    {$kinkyslider_config.headerPaddingTop}px;
        padding-right:  {$kinkyslider_config.headerPaddingRight}px;
        padding-bottom: {$kinkyslider_config.headerPaddingBottom}px;
        padding-left:   {$kinkyslider_config.headerPaddingLeft}px;
        height:         {$kinkyslider_config.headerHeight}px;
    }

    ul#bazingaSliderContent li .bazingaHeader a {

        color:          #{$kinkyslider_config.headerFontColor};
    }

    ul#bazingaSliderContent li .bazingaPrice {

        background:     #{$kinkyslider_config.priceBackground};
        color:          #{$kinkyslider_config.priceFontColor};
        font-family:    {$kinkyslider_config.priceFont};
        font-size:      {$kinkyslider_config.priceFontSize}px;
        line-height:    {$kinkyslider_config.priceLineHeight}px;
        padding-top:    {$kinkyslider_config.pricePaddingTop}px;
        padding-right:  {$kinkyslider_config.pricePaddingRight}px;
        padding-bottom: {$kinkyslider_config.pricePaddingBottom}px;
        padding-left:   {$kinkyslider_config.pricePaddingLeft}px;
        height:         {$kinkyslider_config.headerHeight}px;

    }

    ul#bazingaSliderContent li .bazingaPrice a {

        color:          #{$kinkyslider_config.priceFontColor};
    }

    #kinkyTurnLeft {

        background:     #{$kinkyslider_config.headerBackground};
        color:          #{$kinkyslider_config.headerFontColor};
        left:           {$kinkyslider_config.headerMarginLeft}px;

    }

    #kinkyTurnRight {

        background:     #{$kinkyslider_config.headerBackground};
        color:          #{$kinkyslider_config.headerFontColor};
        right:          {$kinkyslider_config.headerMarginLeft}px;

    }

</style>

<script type="text/javascript" src="{$base_dir}modules/kinkyslider/js/jquery.transform-0.9.3.min.js" charset="utf-8"></script>
<script type="text/javascript" src="{$base_dir}modules/kinkyslider/js/jquery.timers.js" charset="utf-8"></script>
<script type="text/javascript" src="{$base_dir}modules/kinkyslider/js/preloader.js" charset="utf-8"></script>

<div id="bazingaSliderContainer">

    
    <div id="kinkyTurnLeft" class="turnSpan"><span>«</span></div>
    <div id="kinkyTurnRight" class="turnSpan"><span>»</span></div>
    <div id="kinkyPreloader"></div>

    <ul id="bazingaSliderContent" class="bazingaPreloading" >
        {foreach from=$kinkyslider_data key=myId item=kinkyitem}

            {if $kinkyitem.image neq ''}

                    <li>
                    	<a href="{$kinkyitem.link}">
                        <img src="{$base_dir}{$kinkyitem.image}" class="kinkySliderImage" />
                        </a>
                        <div class="makeRelative">
                        {if $kinkyitem.header neq ''}<div class="bazingaHeader"><a href="{$kinkyitem.link}">{$kinkyitem.header}</a></div>{/if}
                        {if $kinkyitem.price neq ''}<div class="bazingaPrice"><a href="{$kinkyitem.link}">{$kinkyitem.price}</a></div>{/if}
                        </div>
                    </li>

            {/if}

        {/foreach}
    </ul>
</div>

{literal}

<script type="text/javascript" charset="utf-8">

    
    var bazingaSlideDelay={/literal}{$kinkyslider_config.speed}{literal};
    var bazingaSlideHeight={/literal}{$kinkyslider_calculated_image_height}{literal};
    var bazingaHeaderFromBottom={/literal}{$kinkyslider_config.headerMarginBottom}{literal};
    var headerOutSwingSpeed= {/literal}{$kinkyslider_config.headerOutSwingSpeed}{literal};
    var headerRotateSpeed= {/literal}{$kinkyslider_config.headerRotateSpeed}{literal};
    var priceAppearSpeed= {/literal}{$kinkyslider_config.priceAppearSpeed}{literal};
    var priceDisappearSpeed= {/literal}{$kinkyslider_config.priceDisappearSpeed}{literal};
    var slideChangeSpeed= {/literal}{$kinkyslider_config.slideChangeSpeed}{literal};

    var ieHackTranslateX={/literal}{$kinkyslider_config.headerMarginLeft}{literal};
    var ieHackTranslateY=-{/literal}{$kinkyslider_config.headerLineHeight+$kinkyslider_config.headerPaddingTop+$kinkyslider_config.headerPaddingBottom+$kinkyslider_config.headerMarginBottom}{literal};

    $(window).load(function(){

        if(!$.support.htmlSerialize && !$.support.opacity) {

            var internetExplorerDetected='Jesus Christ...';

        } else {

            var internetExplorerDetected='Thankfully not!';
        }

        var elementsContainer=$('ul#bazingaSliderContent');
        var allElements=$('ul#bazingaSliderContent li');
        var elementsCount=allElements.size();
        var kinkySliderImages=$('.kinkySliderImage');
        var kinkyContainerWidth=$('#bazingaSliderContainer').width();
        var kinkyContainerHeight=$('#bazingaSliderContainer').height();


        var kinkyTurnLeft=$('#kinkyTurnLeft');
        var kinkyTurnRight=$('#kinkyTurnRight');
        var kinkyTurnHeight=kinkyTurnLeft.height();

        kinkyTurnLeft.css('top',(kinkyContainerHeight/2)-(kinkyTurnHeight/2));
        kinkyTurnRight.css('top',(kinkyContainerHeight/2)-(kinkyTurnHeight/2));

        if (elementsCount<2) { kinkyTurnLeft.hide(); kinkyTurnRight.hide();}

        // we'll mark the last element spat out of the array as 'active' :
        // this is the first element from the top - the first one that appears
        // to the user

        allElements.eq(elementsCount-1).addClass('kinkyActive');
        var findActiveSlide;
        var nextSlide;
        var currentSlide;

        currentSlide=elementsCount-1;        

        // place the price tags next to the header

        allElements.each(function(){

            selectHeader=$(this).find('.bazingaHeader');
            selectPriceTag=$(this).find('.bazingaPrice');
            selectedPriceTagWidth=selectPriceTag.width();
            selectedHeaderWidth=selectHeader.width();            
            selectedHeaderHeight=selectHeader.height();            
            selectedHeaderLeft=selectHeader.css('left');
            selectedHeaderPaddingLeft=parseInt(selectHeader.css('padding-left'));
            selectedHeaderPaddingRight=parseInt(selectHeader.css('padding-right'));
            selectedHeaderPaddingLeftAndRight=selectedHeaderPaddingRight+selectedHeaderPaddingRight;
                        
            priceTagLeft=parseInt(selectedHeaderWidth)+parseInt(selectedHeaderLeft)+selectedHeaderPaddingLeftAndRight;

            selectPriceTag.css('left',priceTagLeft+'px');
            selectPriceTag.data('originalPosition', bazingaHeaderFromBottom + 80);
            /*$(this).click(function(){

                checkIfURL=selectHeader.find('a').attr('href');
                if (checkIfURL!='') {
                    window.location.href=checkIfURL;
                }
                

            });*/

        });

        function bazingaAnimateTo(container, slides, slideNumber, nextSlide, headerOutSwingSpeed, headerRotateSpeed, priceAppearSpeed, priceDisappearSpeed, slideChangeSpeed) {
           
            slides.eq(nextSlide).css('z-index','105');
            slides.eq(nextSlide).css('translate',['0','0']);

            slides.eq(slideNumber).find('.bazingaHeader').animate({translate:['0','70']}, headerOutSwingSpeed,'easeInOutBack');
            slides.eq(nextSlide).find('.bazingaHeader').css('opacity','1.0');
            slides.eq(nextSlide).find('.bazingaHeader').css('rotate','0deg');

            if (internetExplorerDetected=='Jesus Christ...') {

                slides.eq(nextSlide).find('.bazingaHeader').css('translate',[ieHackTranslateX,ieHackTranslateY]);

            } else {

                slides.eq(nextSlide).find('.bazingaHeader').css('translate',['0','0']);

            }

            slides.eq(nextSlide).find('.bazingaPrice').css('opacity','0.0');
            slides.eq(nextSlide).find('.bazingaPrice').css('bottom',slides.eq(nextSlide).find('.bazingaPrice').data('originalPosition')+'px');

            slides.eq(slideNumber).css('z-index','110');
            slides.eq(nextSlide).show();
            slides.eq(nextSlide).find('.bazingaHeader').show();


            slides.eq(slideNumber).find('.bazingaHeader').animate({opacity:'0.0'},100);
            slides.eq(nextSlide).find('.bazingaHeader').animate({rotate:'+=720deg'}, headerRotateSpeed, function(){

                    slides.eq(nextSlide).find('.bazingaPrice').animate({opacity:'1.0',bottom:'-=80'}, priceAppearSpeed, 'easeInOutBack');

            });

            slides.eq(slideNumber).find('.bazingaPrice').animate({opacity:'0.0'}, priceDisappearSpeed);
            
            slides.eq(slideNumber).animate({translate:['0',-bazingaSlideHeight-(20*bazingaSlideHeight/100)]}, slideChangeSpeed, 'easeInOutBack',function(){

                $(this).hide();

            });

            

        }

               
        kinkyPreloader('preloadKinkySliderImages', kinkySliderImages, 500, function() {

            elementsContainer.hide();
            elementsContainer.removeClass('bazingaPreloading');
            elementsContainer.fadeIn(500);

            /* we set the current element to the first element from the top - last from the 'real' top */            
                        
            allElements.eq(currentSlide).find('.bazingaPrice').css('bottom',allElements.eq(currentSlide).find('.bazingaPrice').data('originalPosition')+'px');
            allElements.eq(currentSlide).find('.bazingaPrice').animate({opacity:'1.0',bottom:'-=80'},1000,'easeInOutBack');

            
            if (elementsCount>1) {


                $(document).everyTime(bazingaSlideDelay,'changeSlide',function(){

                    if (!allElements.is(':animated')) {

                        bazingaFindCurrentAndNextSlide('next');

                        bazingaAnimateTo(elementsContainer, allElements, currentSlide, nextSlide, headerOutSwingSpeed, headerRotateSpeed, priceAppearSpeed, priceDisappearSpeed, slideChangeSpeed);
                        oldCurrentSlide=currentSlide;
                        currentSlide--;
                        if (currentSlide<0) { currentSlide=elementsCount-1 }
                        allElements.eq(oldCurrentSlide).removeClass('kinkyActive');
                        allElements.eq(currentSlide).addClass('kinkyActive');
                    }

                });
           }

        }); /* kinkyPreloader() */

        $('#bazingaSliderContainer').mouseout(function(){

            kinkyTurnLeft.css('opacity','0.0');
            kinkyTurnRight.css('opacity','0.0');
        })

        kinkyTurnLeft.click(function(){

            $(document).stopTime('changeSlide');

            if (!allElements.is(':animated')) {

                bazingaFindCurrentAndNextSlide('previous');

                bazingaAnimateTo(elementsContainer, allElements, currentSlide, nextSlide, headerOutSwingSpeed, headerRotateSpeed, priceAppearSpeed, priceDisappearSpeed, slideChangeSpeed);
                oldCurrentSlide=currentSlide;
                currentSlide++;
                if (currentSlide>elementsCount-1) { currentSlide=0 }
                allElements.eq(oldCurrentSlide).removeClass('kinkyActive');
                allElements.eq(currentSlide).addClass('kinkyActive');
            }

        });


        kinkyTurnRight.click(function(){

             $(document).stopTime('changeSlide');

            if (!allElements.is(':animated')) {
            
                bazingaFindCurrentAndNextSlide('next');

                bazingaAnimateTo(elementsContainer, allElements, currentSlide, nextSlide, headerOutSwingSpeed, headerRotateSpeed, priceAppearSpeed, priceDisappearSpeed, slideChangeSpeed);
                oldCurrentSlide=currentSlide;
                currentSlide--;
                if (currentSlide<0) { currentSlide=elementsCount-1 }
                allElements.eq(oldCurrentSlide).removeClass('kinkyActive');
                allElements.eq(currentSlide).addClass('kinkyActive');
            }

        });


        function bazingaFindCurrentAndNextSlide(direction) {

            findActiveSlide=elementsContainer.find('.kinkyActive');
            currentSlide=findActiveSlide.index();

            if (direction=='next') {

                tryNextSlide=currentSlide-1;
                if (tryNextSlide<0) { nextSlide=elementsCount-1; } else {nextSlide=tryNextSlide;}
            }

            if (direction=='previous') {
                tryPreviousSlide=currentSlide+1;
                if (tryPreviousSlide>elementsCount-1) { nextSlide=0 } else {nextSlide=tryPreviousSlide;}
            }
        }

        $('#bazingaSliderContainer').mousemove(function(e){

            kinkyPositionX=e.clientX-$(this).offset().left;
            kinkyTurnLeftOpacityDelta=kinkyPositionX/(kinkyContainerWidth/2);
            kinkyTurnRightOpacityDelta=(kinkyPositionX-(kinkyContainerWidth/2))/(kinkyContainerWidth/2);

                       
            kinkyTurnLeft.css('opacity',1-kinkyTurnLeftOpacityDelta);
            kinkyTurnRight.css('opacity',kinkyTurnRightOpacityDelta);
            

        });
      });


</script>
{/literal}
