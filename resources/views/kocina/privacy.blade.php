<x-kocina.layout>
    <x-slot:title>
        Privacy
    </x-slot>

    {{--    TODO Fix JS errors--}}

    <div class="container page">
        @env('local')
            <h1 class="mb-4 text-2xl font-bold md:text-3xl">
                Privacy
            </h1>
            <p class="mb-6">
                De privacy pagina werkt niet op de development omgeving.
            </p>
        @endenv

        <div id="piwikBlocked" style="display: none;">
            <h1>
                Privacy
            </h1>
            <p>
                Het lijkt erop dat je een ad-blocker gebruikt. Er worden daarom geen gegevens verzameld en je
                privacy is gewaardborgd op deze website.
            </p>
        </div>

        <div id="piwikConsentForm" style="display: none;">
            <div id="ppms_cm_privacy_settings" data-editor-centralize="true" data-main-container="true"
                 data-root="true">
                <div data-disable-select="true">
                    <h1 id="ppms_cm_privacy_settings_form_link_header_id">
                        Privacy
                    </h1>
                    <p id="ppms_cm_privacy_settings_form_link_text_id">
                        Er worden gegevens verzameld om te analyseren hoe de website wordt gebruikt. Er wordt altijd
                        toestemming gevraagd om dit te doen. Je kunt uw privacy-instellingen hier wijzigen.
                    </p>
                    <button id="ppms_cm_privacy_settings_button">
                        Instellingen beheren
                    </button>
                </div>
            </div>

            {{--            <div id='ppms_cm_data_subject' class='ppms_cm_data_subject_widget__wrapper' data-editor-centralize='true'--}}
            {{--                 data-main-container='true' data-root='true'>--}}
            {{--                <h3 id='ppms_cm_data_subject_header' class='header3'>Toegang tot uw gegevens</h3>--}}
            {{--                <p id='ppms_cm_data_subject_paragraph' class='paragraph'>--}}
            {{--                    Vul het formulier in om toegang tot uw gegevens te vragen. We zullen uw e-mailadres alleen--}}
            {{--                    gebruiken om deze aanvraag te verwerken.--}}
            {{--                </p>--}}
            {{--                <form id='ppms_cm_data_subject_form' class='ppms_cm_data_subject_form' data-disable-select='true'>--}}
            {{--                    <label id='ppms_cm_data_subject_form_label' class='ppms_cm_data_subject_form_label'>E-mail</label>--}}
            {{--                    <input type='email' name='email' tabindex='0' required id='ppms_cm_data_subject_form_input'--}}
            {{--                           class='ppms_cm_data_subject_form_input'>--}}
            {{--                    <label id='ppms_cm_data_subject_form_label_request_type' class='ppms_cm_data_subject_form_label'>--}}
            {{--                        Soort verzoek--}}
            {{--                    </label>--}}
            {{--                    <div id='ppms_cm_data_subject_form_select_wrapper' class='ppms_cm_data_subject_form_select_wrapper'--}}
            {{--                         data-disable-select='true' data-select-name='event'>--}}
            {{--                        <div id='ppms_cm_data_subject_form_select_btn' tabindex='0' data-type='customSelect'--}}
            {{--                             class='ppms_select_component_btn'>--}}
            {{--                            <span id='ppms_cm_data_subject_form_select_btn_text'>Toegang tot mijn gegevens</span>--}}
            {{--                            <span class='ppms_select_component-btn-chevron'>--}}
            {{--                                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16'--}}
            {{--                                             viewBox='0 0 16 16'>--}}
            {{--                                            <path fill='#131313'--}}
            {{--                                                  d='M8.2,8.7l2.8-2.8l1.4,1.4l-2.8,2.8l-1.4,1.4L4,7.2l1.4-1.4L8.2,8.7z'/>--}}
            {{--                                        </svg>--}}
            {{--                                    </span>--}}
            {{--                        </div>--}}
            {{--                        <div id='ppms_cm_data_subject_form_select_extendable' class='ppms_select_component_extendable'--}}
            {{--                             data-disable-select='true' data-type='customSelectWrapper'>--}}
            {{--                            <div id='ppms_cm_data_subject_form_select_extended_item_1' tabindex='0'--}}
            {{--                                 class='ppms_select_component_extended_item' data-type='customOption'--}}
            {{--                                 data-value='view_data'>--}}
            {{--                                Toegang tot mijn gegevens--}}
            {{--                            </div>--}}
            {{--                            <div id='ppms_cm_data_subject_form_select_extended_item_2'--}}
            {{--                                 class='ppms_select_component_extended_item' tabindex='0' data-type='customOption'--}}
            {{--                                 data-value='delete_data'>--}}
            {{--                                Mijn gegevens verwijderen--}}
            {{--                            </div>--}}
            {{--                            <div id='ppms_cm_data_subject_form_select_extended_item_3'--}}
            {{--                                 class='ppms_select_component_extended_item' tabindex='0' data-type='customOption'--}}
            {{--                                 data-value='change_data'>--}}
            {{--                                Mijn gegevens wijzigen--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                    <label id='ppms_cm_data_subject_form_label_textarea' class='ppms_cm_data_subject_form_label'>--}}
            {{--                        Bericht--}}
            {{--                    </label>--}}
            {{--                    <textarea name='message' rows='7' id='ppms_cm_data_subject_form_textarea'--}}
            {{--                              class='ppms_cm_data_subject_form_textarea' tabindex='0'/>--}}
            {{--                    <input type='submit' id='ppms_cm_data_subject_form_submit' class='ppms_cm_data_subject_form_submit'--}}
            {{--                           tabindex='0' value='Verzenden'>--}}
            {{--                </form>--}}
            {{--            </div>--}}
        </div>
    </div>

    {{--    @push("script")--}}
    {{--        <script>--}}
    {{--            (function (window, document) {--}}
    {{--                // Check if Piwik is loaded.--}}
    {{--                setTimeout(function () {--}}
    {{--                    if (ppms?.cm?.api.length > 0) {--}}
    {{--                        document.getElementById('piwikConsentForm').style.display = 'block';--}}
    {{--                    } else {--}}
    {{--                        document.getElementById('piwikBlocked').style.display = 'block';--}}
    {{--                    }--}}
    {{--                }, 1000);--}}
    {{--            })(window, document);--}}
    {{--        </script>--}}

    {{--        --}}{{-- @formatter:off --}}
{{--        <script>(function(window,document){var PREFIX='ppms_select_component';var CLASSES={DROP_DOWN:PREFIX+'_btn',ITEM:PREFIX+'_extended_item',LIST_EXPANDED:'expanded',ITEM_ACTIVE:PREFIX+'_extended_item-active'};var SELECTORS={WRAPPER:'.ppms_cm_data_subject_form_select_wrapper',DROP_DOWN:'.'+CLASSES.DROP_DOWN,LABEL:'.'+PREFIX+'_btn span',LIST:'.'+PREFIX+'_extendable',ITEM:'.'+CLASSES.ITEM};var RX_CLOSE=new RegExp('\b'+CLASSES.ITEM+'|'+CLASSES.DROP_DOWN+'\b');var wrappers=document.querySelectorAll(SELECTORS.WRAPPER);var i;var timer={};for(i=0;i<wrappers.length;++i){init(wrappers[i],i);}function init(wrapper,index){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);addSelect(wrapper,index);var form=(function(el){while(!(el=el.parentElement)|el.tagName.toUpperCase()==='FORM');return el;})(wrapper);if(form!==null){addEventListener(form,'reset',function(){var optionsContainer=wrapper.querySelector(SELECTORS.LIST);if(!optionsContainer||optionsContainer.children.length===0)return;chooseOption(wrapper,select,optionsContainer.children[0]);});addEventListener(form,'submit',function(event){event=event||window.event;window.sendUserDataSubjectRequest(event.target);preventDefault(event);});}addEventListener(select,'mousedown',function(event){event=event||window.event;if(event.button===0||and(isIE8(),event.button===1)){toggleSelect(wrapper);}});addEventListener(select,['focus','focusin'],function(){focusHandler(index);openSelect(wrapper);});addEventListener(select,['blur','focusout'],function(event){blurHandler(wrapper,index,event);});addEventListener(select,'keydown',function(event){event=event||window.event;switch(event.code|event.key|event.keyCode){case'ArrowDown':case'Down':case 40:preventDefault(event);openSelect(wrapper);wrapper.querySelector(SELECTORS.ITEM).focus();break;case'Tab':case 9:if(event.shiftKey){closeSelect(wrapper);}else if(isSelectOpened(wrapper)){preventDefault(event);wrapper.querySelector(SELECTORS.ITEM).focus();}break;case'Enter':case 13:case'Space':case'Spacebar':case' ':case 32:preventDefault(event);toggleSelect(wrapper);break;}});}function blurHandler(wrapper,index,event){event=event||window.event;if(!event.relatedTarget||!event.relatedTarget.className.toString().match(RX_CLOSE)){timer[index]=setTimeout(function(){closeSelect(wrapper);},5);}}function and(a,b){return!(!a||!b);}function isIE8(){return and(document.all,!document.addEventListener);}function preventDefault(event){if('preventDefault'in event){event.preventDefault();}else{event.returnValue=false;}}function addSelect(wrapper,index){var optionsContainer=wrapper.querySelector(SELECTORS.LIST);var select,i,option,origOption;select=createSelect(wrapper);for(i=0;i<optionsContainer.children.length;++i){origOption=optionsContainer.children[i];if(origOption.nodeType===8){continue;}option=createOption(wrapper,index,select,origOption);select.appendChild(option);}wrapper.appendChild(select);}function createSelect(wrapper){var select=document.createElement('select');select.setAttribute('hidden','hidden');select.setAttribute('aria-hidden','true');select.setAttribute('name',wrapper.getAttribute('data-select-name'));return select;}function createOption(wrapper,index,select,origOption){var option=document.createElement('option');option.setAttribute('value',origOption.getAttribute('data-value'));option.setAttribute('label',origOption.innerHTML);option.innerHTML=origOption.innerHTML;addEventListener(origOption,'mousedown',function(event){event=event||window.event;if(event.button===0||and(isIE8(),event.button===1)){chooseOption(wrapper,select,origOption);}});addEventListener(origOption,'keydown',function(event){var node;event=event||window.event;switch(event.code|event.key|event.keyCode){case'Enter':case 13:case'Space':case'Spacebar':case' ':case 32:preventDefault(event);chooseOption(wrapper,select,origOption);break;case'ArrowUp':case'Up':case 38:preventDefault(event);node=previousElementSibling(origOption);if(node){node.focus();}else{lastElementChild(origOption.parentNode).focus();}break;case'ArrowDown':case'Down':case 40:preventDefault(event);node=nextElementSibling(origOption);if(node){node.focus();}else{firstElementChild(origOption.parentNode).focus();}break;}});addEventListener(origOption,['blur','focusout'],function(event){blurHandler(wrapper,index,event);});addEventListener(origOption,['focus','focusin'],function(){focusHandler(index);});return option;}function focusHandler(index){if(timer[index]){clearTimeout(timer[index]);timer[index]=null;}}function chooseOption(wrapper,select,option){var label=wrapper.querySelector(SELECTORS.LABEL);select.value=option.getAttribute('data-value');label.innerHTML=option.innerHTML;closeSelect(wrapper);}function isSelectOpened(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);return hasClass(select,CLASSES.LIST_EXPANDED);}function openSelect(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);addClass(select,CLASSES.LIST_EXPANDED);}function closeSelect(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);removeClass(select,CLASSES.LIST_EXPANDED);}function toggleSelect(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);toggleClass(select,CLASSES.LIST_EXPANDED);}function addEventListener(element,events,handler){var i;if(!(events instanceof Array)){events=[events];}for(i=0;i<events.length;++i){if(element.addEventListener){element.addEventListener(events[i],handler,false);}else{element.attachEvent('on'+events[i],handler);}}}function addClass(element,className){if(element.className.indexOf(className)===-1){element.className+=' '+className;}}function removeClass(element,className){if(element.className.indexOf(className)!==-1){element.className=element.className.replace(' '+className,'');}}function toggleClass(element,className){if(element.className.indexOf(className)!==-1){element.className=element.className.replace(' '+className,'');}else{element.className+=' '+className;}}function hasClass(element,className){return element.className.indexOf(className)!==-1;}function previousElementSibling(element){var node;if('previousElementSibling'in element){return element.previousElementSibling;}while(node=element.previousSibling){if(node.nodeType===1){return node;}}return null;}function nextElementSibling(element){var node;if('nextElementSibling'in element){return element.nextElementSibling;}while(node=element.nextSibling){if(node.nodeType===1){return node;}}return null;}function firstElementChild(element){var node,i=0;if('firstElementChild'in element){return element.firstElementChild;}while(node=element.childNodes[i++]){if(node.nodeType===1){return node;}}return null;}function lastElementChild(element){var node,i=0;if('lastElementChild'in element){return element.lastElementChild;}while(node=element.childNodes[element.childNodes.length- ++i]){if(node.nodeType===1){return node;}}return null;}}(window,document));</script>--}}
{{--        --}}{{-- @formatter:on --}}
    {{--    @endpush--}}
</x-kocina.layout>
