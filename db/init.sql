
SELECT 'grant exec on ' + Quotename(routine_schema) + '.' + Quotename(routine_name) +
' TO sos' FROM information_schema.routines WHERE Objectproperty(Object_id(routine_name), 'IsMSShipped') = 0 

grant exec on [dbo].[fn_salesuserstatus] TO sos
grant exec on [dbo].[fn_purchuserstatus] TO sos
grant exec on [dbo].[fn_bcsalesuserstatus] TO sos
grant exec on [dbo].[Date2UnixTimeStamp] TO sos
grant exec on [dbo].[fn_xmlspecialchars] TO sos
grant exec on [dbo].[fn_sos_onhandratio] TO sos
grant exec on [dbo].[fn_setemailmsg] TO sos
grant exec on [dbo].[fn_GetCountCancelByMember] TO sos
grant exec on [dbo].[fn_GetCountEmptyStock] TO sos
grant exec on [dbo].[fn_GetCountLatePayment] TO sos
grant exec on [dbo].[fn_GetCountNotSuccessOrderBc] TO sos
grant exec on [dbo].[fn_GetCountRevisi] TO sos
grant exec on [dbo].[fn_GetCountSuccessOrder] TO sos
grant exec on [dbo].[fn_GetCountTechnicalError] TO sos
grant exec on [dbo].[fn_GetCountSuccessOrderBc] TO sos
grant exec on [dbo].[sp_updateSalesTotal] TO sos
grant exec on [dbo].[sp_salesByPassed] TO sos
grant exec on [dbo].[sp_paygate_PaymSuccess] TO sos
grant exec on [dbo].[sp_paygate_PaymReconcile] TO sos
grant exec on [dbo].[sp_paygate_PaymInit] TO sos
grant exec on [dbo].[sp_paygate_PaymFail] TO sos
grant exec on [dbo].[sp_paygate_PaymCreate] TO sos
grant exec on [dbo].[sp_getNextNo] TO sos
grant exec on [dbo].[sp_createSync] TO sos
grant exec on [dbo].[sp_checkQuantity] TO sos
grant exec on [dbo].[sp_updateSalesLine] TO sos
grant exec on [dbo].[sp_updatepurchTotal] TO sos
grant exec on [dbo].[sp_updatePaymentMode] TO sos
grant exec on [dbo].[sp_sos_INVENTTABLESYNC] TO sos
grant exec on [dbo].[sp_sos_IMPORTMEMBER] TO sos
grant exec on [dbo].[sp_sos_UPDATEMEMBERMAPPING] TO sos
grant exec on [dbo].[sp_AXProcess] TO sos
grant exec on [dbo].[sp_sendEmailAndSMS] TO sos
grant exec on [dbo].[sp_SalesConfirmQtyChange] TO sos
grant exec on [dbo].[sp_updatePurchLine] TO sos
grant exec on [dbo].[sp_updateSalesStatus2] TO sos
grant exec on [dbo].[sp_updateSalesStatus] TO sos
grant exec on [dbo].[sp_checkSyncOrder] TO sos
grant exec on [dbo].[sp_cancelSalesNotYetPaid] TO sos

insert into webdev.dbo.sysparamtable select * from webdev2.dbo.sysparamtable
insert into webdev.dbo.smstemplate select * from webdev2.dbo.smstemplate
insert into webdev.dbo.emailtemplate select * from webdev2.dbo.emailtemplate
insert into webdev.dbo.sequencetable select * from webdev2.dbo.sequencetable
insert into webdev.dbo.paymentmode select * from webdev2.dbo.paymentmode
insert into webdev.dbo.parametertable select * from webdev2.dbo.parametertable
insert into webdev.dbo.inventMappingbrand select * from web.dbo.inventMappingbrand
insert into webdev.dbo.inventgroup select * from webdev2.dbo.inventgroup