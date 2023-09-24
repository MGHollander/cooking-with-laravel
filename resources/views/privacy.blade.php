@extends('layout')

{{-- This page is not working locally because it is missing the piwik code --}}

@section('head')
    @vite('resources/js/app.js')
@endsection

@section('title')
    <title>Privacy - {{ config('app.name') }}</title>
@endsection

@section('content')
    <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
        <div>
            <a href="{{ route('home') }}">
                {{-- TODO Place the logo on a shared place to load it here and in the ApplicationLogo component. --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 46"
                     class="h-16 w-16 fill-current text-emerald-700">
                    <g fill="currentColor">
                        <path
                            d="M15.175 36.263l4.786 2.394a1.45 1.45 0 0 0 1.297 0l4.786-2.394a1.45 1.45 0 0 0 .802-1.298v-7.181c0-.801-.65-1.45-1.45-1.45h-9.573c-.8 0-1.45.649-1.45 1.45v7.181c0 .55.31 1.052.802 1.298zm2.099-7.029h6.671v4.835l-3.335 1.668-3.336-1.668v-4.835z"
                        />
                        <path
                            d="M39.753 19.151h-8.166V8.632a1.451 1.451 0 0 0-1.45-1.451h-2.1C27.9 3.198 24.622 0 20.61 0c-4.014 0-7.292 3.199-7.427 7.18h-2.1c-.801.001-1.45.65-1.45 1.452V19.15H1.464a1.45 1.45 0 0 0 0 2.902h3.261L3.722 42.149a3.63 3.63 0 0 0 .997 2.71 3.627 3.627 0 0 0 2.656 1.132h26.468a3.628 3.628 0 0 0 2.656-1.132 3.63 3.63 0 0 0 .997-2.71l-1.004-20.097h3.261a1.45 1.45 0 0 0 0-2.902v.001zM20.61 2.901a4.539 4.539 0 0 1 4.524 4.28h-9.049a4.538 4.538 0 0 1 4.525-4.28zm-8.077 7.182h16.153v9.067H12.533v-9.067zM34.6 42.294a.755.755 0 0 1-.202.565.755.755 0 0 1-.554.23H7.375a.753.753 0 0 1-.554-.23.756.756 0 0 1-.202-.565L7.63 22.053h25.958L34.6 42.294z"
                        />
                    </g>
                </svg>
            </a>
        </div>

        <div id="piwikBlocked" class="hidden">
            <div class="mt-8 w-full overflow-hidden bg-white p-8 shadow-md sm:max-w-lg sm:rounded-lg">
                <h1 class="mb-4 text-2xl font-bold md:text-3xl">
                    Privacy
                </h1>
                <p class="mb-6">
                    Het lijkt erop dat je een ad-blocker gebruikt. Er worden daarom geen gegevens verzameld en je
                    privacy is gewaardborgd op deze website.
                </p>
            </div>
        </div>

        <div id="piwikConsentForm" class="hidden flex-col items-center ">
            <div class="mt-8 w-full overflow-hidden bg-white p-8 shadow-md sm:max-w-lg sm:rounded-lg">
                <div id="ppms_cm_privacy_settings" data-editor-centralize="true" data-main-container="true"
                     data-root="true">
                    <div data-disable-select="true">
                        <h1 id="ppms_cm_privacy_settings_form_link_header_id"
                            class="mb-4 text-2xl font-bold md:text-3xl">
                            Privacy
                        </h1>
                        <p id="ppms_cm_privacy_settings_form_link_text_id" class="mb-6">
                            Er worden gegevens verzameld om te analyseren hoe de website wordt gebruikt. Er wordt altijd
                            toestemming gevraagd om dit te doen. Je kunt uw privacy-instellingen hier wijzigen.
                        </p>
                        <button
                            id="ppms_cm_privacy_settings_button"
                            class="lg:text-md rounded-md border-emerald-700 bg-emerald-700 p-2 text-sm text-white hover:bg-emerald-900 sm:px-3 md:text-base"
                        >
                            Instellingen beheren
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8 w-full overflow-hidden bg-white p-8 shadow-md sm:max-w-2xl sm:rounded-lg">
                <div id='ppms_cm_data_subject' class='ppms_cm_data_subject_widget__wrapper'
                     data-editor-centralize='true'
                     data-main-container='true' data-root='true'>
                    <h3 id='ppms_cm_data_subject_header' class='header3'>
                        Toegang tot uw gegevens</h3>
                    <p id='ppms_cm_data_subject_paragraph' class='paragraph'>
                        Vul het formulier in om toegang tot uw gegevens te vragen. We zullen uw e-mailadres alleen
                        gebruiken
                        om deze aanvraag te verwerken.
                    </p>
                    <form id='ppms_cm_data_subject_form' class='ppms_cm_data_subject_form' data-disable-select='true'>
                        <label
                            id='ppms_cm_data_subject_form_label'
                            class='ppms_cm_data_subject_form_label'>E-mail</label>
                        <input type='email' name='email'
                               tabindex='0' required
                               id='ppms_cm_data_subject_form_input'
                               class='ppms_cm_data_subject_form_input'>
                        <label id='ppms_cm_data_subject_form_label_request_type'
                               class='ppms_cm_data_subject_form_label'>
                            Soort
                            verzoek
                        </label>
                        <div id='ppms_cm_data_subject_form_select_wrapper'
                             class='ppms_cm_data_subject_form_select_wrapper'
                             data-disable-select='true' data-select-name='event'>
                            <div id='ppms_cm_data_subject_form_select_btn' tabindex='0' data-type='customSelect'
                                 class='ppms_select_component_btn'>
                                <span id='ppms_cm_data_subject_form_select_btn_text'>Toegang tot mijn gegevens</span>
                                <span class='ppms_select_component-btn-chevron'>
                                <svg xmlns='http://www.w3.org/2000/svg'
                                     width='16' height='16'
                                     viewBox='0 0 16 16'><path fill='#131313'
                                                               d='M8.2,8.7l2.8-2.8l1.4,1.4l-2.8,2.8l-1.4,1.4L4,7.2l1.4-1.4L8.2,8.7z'></path>
                                </svg>
                            </span>
                            </div>
                            <div id='ppms_cm_data_subject_form_select_extendable'
                                 class='ppms_select_component_extendable'
                                 data-disable-select='true' data-type='customSelectWrapper'>
                                <div id='ppms_cm_data_subject_form_select_extended_item_1' tabindex='0'
                                     class='ppms_select_component_extended_item' data-type='customOption'
                                     data-value='view_data'>Toegang tot mijn gegevens
                                </div>
                                <div id='ppms_cm_data_subject_form_select_extended_item_2'
                                     class='ppms_select_component_extended_item' tabindex='0' data-type='customOption'
                                     data-value='delete_data'>Mijn gegevens verwijderen
                                </div>
                                <div id='ppms_cm_data_subject_form_select_extended_item_3'
                                     class='ppms_select_component_extended_item' tabindex='0' data-type='customOption'
                                     data-value='change_data'>Mijn gegevens wijzigen
                                </div>
                            </div>
                        </div>
                        <label id='ppms_cm_data_subject_form_label_textarea'
                               class='ppms_cm_data_subject_form_label'>Bericht</label>
                        <textarea name='message' rows='7'
                                  id='ppms_cm_data_subject_form_textarea'
                                  class='ppms_cm_data_subject_form_textarea'
                                  tabindex='0'></textarea><input
                            type='submit' id='ppms_cm_data_subject_form_submit' class='ppms_cm_data_subject_form_submit'
                            tabindex='0' value='Verzenden'></form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function (window, document) {
            // Check if Piwik is loaded.
            setTimeout(function () {
                console.log('Piwik is loaded: ' + (typeof Piwik !== 'undefined'));
                if (typeof Piwik !== 'undefined' && Piwik.initialized) {
                    document.getElementById('piwikConsentForm').classList.remove('hidden');
                    document.getElementById('piwikConsentForm').classList.add('flex');
                } else {
                    document.getElementById('piwikBlocked').classList.remove('hidden');
                }
            }, 1000);
        })(window, document);
    </script>

    {{-- @formatter:off --}}
    <script>(function(window,document){var PREFIX='ppms_select_component';var CLASSES={DROP_DOWN:PREFIX+'_btn',ITEM:PREFIX+'_extended_item',LIST_EXPANDED:'expanded',ITEM_ACTIVE:PREFIX+'_extended_item-active'};var SELECTORS={WRAPPER:'.ppms_cm_data_subject_form_select_wrapper',DROP_DOWN:'.'+CLASSES.DROP_DOWN,LABEL:'.'+PREFIX+'_btn span',LIST:'.'+PREFIX+'_extendable',ITEM:'.'+CLASSES.ITEM};var RX_CLOSE=new RegExp('\b'+CLASSES.ITEM+'|'+CLASSES.DROP_DOWN+'\b');var wrappers=document.querySelectorAll(SELECTORS.WRAPPER);var i;var timer={};for(i=0;i<wrappers.length;++i){init(wrappers[i],i);}function init(wrapper,index){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);addSelect(wrapper,index);var form=(function(el){while(!(el=el.parentElement)|el.tagName.toUpperCase()==='FORM');return el;})(wrapper);if(form!==null){addEventListener(form,'reset',function(){var optionsContainer=wrapper.querySelector(SELECTORS.LIST);if(!optionsContainer||optionsContainer.children.length===0)return;chooseOption(wrapper,select,optionsContainer.children[0]);});addEventListener(form,'submit',function(event){event=event||window.event;window.sendUserDataSubjectRequest(event.target);preventDefault(event);});}addEventListener(select,'mousedown',function(event){event=event||window.event;if(event.button===0||and(isIE8(),event.button===1)){toggleSelect(wrapper);}});addEventListener(select,['focus','focusin'],function(){focusHandler(index);openSelect(wrapper);});addEventListener(select,['blur','focusout'],function(event){blurHandler(wrapper,index,event);});addEventListener(select,'keydown',function(event){event=event||window.event;switch(event.code|event.key|event.keyCode){case'ArrowDown':case'Down':case 40:preventDefault(event);openSelect(wrapper);wrapper.querySelector(SELECTORS.ITEM).focus();break;case'Tab':case 9:if(event.shiftKey){closeSelect(wrapper);}else if(isSelectOpened(wrapper)){preventDefault(event);wrapper.querySelector(SELECTORS.ITEM).focus();}break;case'Enter':case 13:case'Space':case'Spacebar':case' ':case 32:preventDefault(event);toggleSelect(wrapper);break;}});}function blurHandler(wrapper,index,event){event=event||window.event;if(!event.relatedTarget||!event.relatedTarget.className.toString().match(RX_CLOSE)){timer[index]=setTimeout(function(){closeSelect(wrapper);},5);}}function and(a,b){return!(!a||!b);}function isIE8(){return and(document.all,!document.addEventListener);}function preventDefault(event){if('preventDefault'in event){event.preventDefault();}else{event.returnValue=false;}}function addSelect(wrapper,index){var optionsContainer=wrapper.querySelector(SELECTORS.LIST);var select,i,option,origOption;select=createSelect(wrapper);for(i=0;i<optionsContainer.children.length;++i){origOption=optionsContainer.children[i];if(origOption.nodeType===8){continue;}option=createOption(wrapper,index,select,origOption);select.appendChild(option);}wrapper.appendChild(select);}function createSelect(wrapper){var select=document.createElement('select');select.setAttribute('hidden','hidden');select.setAttribute('aria-hidden','true');select.setAttribute('name',wrapper.getAttribute('data-select-name'));return select;}function createOption(wrapper,index,select,origOption){var option=document.createElement('option');option.setAttribute('value',origOption.getAttribute('data-value'));option.setAttribute('label',origOption.innerHTML);option.innerHTML=origOption.innerHTML;addEventListener(origOption,'mousedown',function(event){event=event||window.event;if(event.button===0||and(isIE8(),event.button===1)){chooseOption(wrapper,select,origOption);}});addEventListener(origOption,'keydown',function(event){var node;event=event||window.event;switch(event.code|event.key|event.keyCode){case'Enter':case 13:case'Space':case'Spacebar':case' ':case 32:preventDefault(event);chooseOption(wrapper,select,origOption);break;case'ArrowUp':case'Up':case 38:preventDefault(event);node=previousElementSibling(origOption);if(node){node.focus();}else{lastElementChild(origOption.parentNode).focus();}break;case'ArrowDown':case'Down':case 40:preventDefault(event);node=nextElementSibling(origOption);if(node){node.focus();}else{firstElementChild(origOption.parentNode).focus();}break;}});addEventListener(origOption,['blur','focusout'],function(event){blurHandler(wrapper,index,event);});addEventListener(origOption,['focus','focusin'],function(){focusHandler(index);});return option;}function focusHandler(index){if(timer[index]){clearTimeout(timer[index]);timer[index]=null;}}function chooseOption(wrapper,select,option){var label=wrapper.querySelector(SELECTORS.LABEL);select.value=option.getAttribute('data-value');label.innerHTML=option.innerHTML;closeSelect(wrapper);}function isSelectOpened(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);return hasClass(select,CLASSES.LIST_EXPANDED);}function openSelect(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);addClass(select,CLASSES.LIST_EXPANDED);}function closeSelect(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);removeClass(select,CLASSES.LIST_EXPANDED);}function toggleSelect(wrapper){var select=wrapper.querySelector(SELECTORS.DROP_DOWN);toggleClass(select,CLASSES.LIST_EXPANDED);}function addEventListener(element,events,handler){var i;if(!(events instanceof Array)){events=[events];}for(i=0;i<events.length;++i){if(element.addEventListener){element.addEventListener(events[i],handler,false);}else{element.attachEvent('on'+events[i],handler);}}}function addClass(element,className){if(element.className.indexOf(className)===-1){element.className+=' '+className;}}function removeClass(element,className){if(element.className.indexOf(className)!==-1){element.className=element.className.replace(' '+className,'');}}function toggleClass(element,className){if(element.className.indexOf(className)!==-1){element.className=element.className.replace(' '+className,'');}else{element.className+=' '+className;}}function hasClass(element,className){return element.className.indexOf(className)!==-1;}function previousElementSibling(element){var node;if('previousElementSibling'in element){return element.previousElementSibling;}while(node=element.previousSibling){if(node.nodeType===1){return node;}}return null;}function nextElementSibling(element){var node;if('nextElementSibling'in element){return element.nextElementSibling;}while(node=element.nextSibling){if(node.nodeType===1){return node;}}return null;}function firstElementChild(element){var node,i=0;if('firstElementChild'in element){return element.firstElementChild;}while(node=element.childNodes[i++]){if(node.nodeType===1){return node;}}return null;}function lastElementChild(element){var node,i=0;if('lastElementChild'in element){return element.lastElementChild;}while(node=element.childNodes[element.childNodes.length- ++i]){if(node.nodeType===1){return node;}}return null;}}(window,document));</script>
    {{-- @formatter:on --}}
@endsection
