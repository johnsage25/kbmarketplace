<font size="2" face="Open-sans, sans-serif" color="#555454">
    <table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse"><!-- Title -->
        <thead>
            <tr>
                <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">Reference</th>
                <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">Product</th>
                <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">Unit price</th>
                <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">Quantity</th>
                <th style="border:1px solid #D6D4D4;background-color:#fbfbfb;font-family:Arial;color:#333;font-size:13px;padding:10px">Total price</th>
            </tr>
        </thead>
        <tbody>
            {foreach $product_html_vars['products'] as $product}
                <tr>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td>
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        {$product['reference']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td>
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        <strong>{$product['name']}</strong>
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td align="right">
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        {$product['unit_price']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td align="right">
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        {$product['quantity']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td align="right">
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        {$product['price']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {foreach $product['customization'] as $customization}
                    <tr>
                    <td colspan="2" style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td>
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        <strong>{$product['name']}</strong><br>
                                        {$customization['customization_text']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td align="right">
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        {$product['unit_price']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td align="right">
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        {$customization['customization_quantity']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td style="border:1px solid #D6D4D4;">
                        <table class="table">
                            <tr>
                                <td width="10">&nbsp;</td>
                                <td align="right">
                                    <font size="2" face="Open-sans, sans-serif" color="#555454">
                                        {$customization['quantity']}
                                    </font>
                                </td>
                                <td width="10">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/foreach}
            {/foreach}
            <tr class="conf_body">
                <td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                    <table class="table" style="width:100%;border-collapse:collapse">
                        <tr>
                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                            <td align="right" style="color:#333;padding:0">
                                <font size="2" face="Open-sans, sans-serif" color="#555454">
                                    <strong>Total paid</strong>
                                </font>
                            </td>
                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
                    <table class="table" style="width:100%;border-collapse:collapse">
                        <tr>
                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                            <td align="right" style="color:#333;padding:0">
                                <font size="4" face="Open-sans, sans-serif" color="#555454">
                                    {$product_html_vars['total_paid']}
                                </font>
                            </td>
                            <td width="10" style="color:#333;padding:0">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</font>
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}
