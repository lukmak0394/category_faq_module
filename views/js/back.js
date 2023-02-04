/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function() {

    const controller_url = $('.test-conn-btn').data('urlAttribute');
    const token = $('.test-conn-btn').data('tokenAttribute');

    let language_id = $('#languages-list').val();


    const updateElement = (element,html) => {
        element.html(html);
    }
    
    const addFaqElement = (element,content) => {
        element.prepend(content)
    }

    // This is to test connection only - not longer useful
    // $(document).on('click', '.test-conn-btn',function() {


    //     ajaxTestConn(controller_url,token);
    // })

    // const ajaxTestConn = (controller_url,token) => {
    //     $.ajax({
    //         type: 'POST',
    //         data: `ajax=1&action=test&token=${token}`,
    //         url: controller_url,
    //         dataType: 'json',
    //         success: (res) => {
    //             console.log(res);
    //         },
    //         error: () => {
    //             console.log('error');
    //         }
            
    //     })
    // }

    // Here i get FAQ related to selected category - START
    $(document).on('change','#categories-list',function() {
        const catId = $(this).val();

        const catName = $('#categories-list').find(`option#option-cat-${catId}`).text();

        ajaxGetCategoryFaq(controller_url,token,catId,catName,language_id);

    })

    $(document).on('change','#languages-list',function() {
        language_id = $(this).val();

        const catId = $('#categories-list').val();

        const catName = $('#categories-list').find(`option#option-cat-${catId}`).text();

        ajaxGetCategoryFaq(controller_url,token,catId,catName,language_id);


    })

    const ajaxGetCategoryFaq = (controller_url,token,catId,catName,language_id) => {
        $.ajax({
            type: 'POST',
            data: `ajax=1&action=getCategoryFaq&token=${token}&id_category=${catId}&category_name=${catName}&id_lang=${language_id}`,
            url: controller_url,
            dataType: 'json',
            success: (res) => {
                updateElement($('.category-faq-content'),res);
            },
            error: () => {
                console.log('error');
            }
            
        })
    }
    // Here i get FAQ related to selected category - END


    // Send data to controller and put in DB - START
    $(document).on('click','.submit-qa',function(e) {

        const question = $('#question').val();
        const answer = $('#answer').val();
        const languageId = language_id
        const catId = $('#categories-list').val();

        ajaxSubmitQaValues(controller_url,token,question,answer,languageId,catId)

    })

    const ajaxSubmitQaValues = (controller_url,token,question,answer,languageId,catId) => {
        $.ajax({
            type: 'POST',
            data: `ajax=1&action=getQaValues&token=${token}&question=${question}&answer=${answer}&language_id=${languageId}&id_category=${catId}`,
            url: controller_url,
            dataType: 'json',
            success: (res) => {
                addFaqElement($('.faq-elements-wrapper'),res);
            },
            error: () => {
                console.log('error');
            }
        })
    }
    // Send data to controller and put in DB - END

 
    // Delete FAQ - START

    $(document).on('click','.delete-faq-btn',function(e) {

        const faqId = $(this).data('idFaq')
        
        ajaxDeleteFaqElement(controller_url,token,faqId);

        $(this).parent().parent().remove();
    })

    const ajaxDeleteFaqElement = (controller_url,token,faqId) => {
        $.ajax({
            type: 'POST',
            data: `ajax=1&action=deleteFaqElement&token=${token}&id_faq=${faqId}`,
            url: controller_url,
            dataType: 'json',
            success: (res) => {
                console.log('deleted');
            },
            error: () => {
                console.log('error');
            }
        })
    }

    // Delete FAQ - END

    // Update FAQ - START
    $(document).on('click','.update-faq-btn',function(e) {

        const faqId = $(this).data('idFaq')
        const question = $(this).parent().parent().find('input#existing-question').val()
        const answer = $(this).parent().parent().find('input#existing-answer').val()


            
        ajaxUpdateElement(controller_url,token,faqId,question,answer);
    
       
    })
    
    const ajaxUpdateElement = (controller_url,token,faqId,question,answer) => {


        $.ajax({
            type: 'POST',
            data: `ajax=1&action=updateFaqElement&token=${token}&id_faq=${faqId}&question=${question}&answer=${answer}`,
            url: controller_url,
            dataType: 'json',
            success: (res) => {
                console.log('update');
            },
            error: () => {
                console.log('error');
            }
        })
    }
    // Update FAQ - END

    
})