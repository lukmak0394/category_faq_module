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

    const updateElement = (element,html) => {
        $(element).html(html);
    }
    
    // $(document).on('click', '.test-conn-btn',function() {


    //     ajaxTestConn(controller_url,token);
    // })

    const ajaxTestConn = (controller_url,token) => {
        $.ajax({
            type: 'POST',
            data: `ajax=1&action=test&token=${token}`,
            url: controller_url,
            dataType: 'json',
            success: (res) => {
                console.log(res);
            },
            error: () => {
                console.log('error');
            }
            
        })
    }

    $(document).on('change','#categories-list',function() {
        const catId = $(this).val();

        const catName = $('#categories-list').find(`option#option-cat-${catId}`).text();

        ajaxGetCategoryFaq(controller_url,token,catId,catName);

    
    })

    const ajaxGetCategoryFaq = (controller_url,token,catId,catName) => {
        $.ajax({
            type: 'POST',
            data: `ajax=1&action=getCategoryFaq&token=${token}&id_category=${catId}&category_name=${catName}`,
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

    
})