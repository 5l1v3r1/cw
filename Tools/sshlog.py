#!/usr/bin/python
# -*- coding: UTF-8 -*-

import pymysql
import sys
from pytz import utc
from pytz import timezone
from datetime import datetime
import os
import requests
import json
def geninsertsql(tname,items):
	insert_sql = "insert into {0} ({1}) values ({2})".format(tname,
																','.join(items),
																', '.join(['%s'] * len(items)))
	return insert_sql
def sshrecord(username,clientip):
	cst_tz = timezone('Asia/Shanghai')
	utc_tz = timezone('UTC')
	utcnow = datetime.utcnow()
	utcnow = utcnow.replace(tzinfo=utc_tz)
	cdate = utcnow.astimezone(cst_tz)
	#print( "china : %s"%cdate.strftime('%Y-%m-%d %H:%M:%S'))
	#curl http://ipinfo.io/223.155.166.172  163.19.9.247
	url = "http://ip.taobao.com/service/getIpInfo.php?ip="+str(clientip)
	region = "cn"
	flag = 0
	while flag <= 3:
		try:
			res = requests.get(url)
			#resjson = json.dumps(res.text)
			rj = json.loads(res.text)
			#print(rj["data"])
			if(rj["data"]["region"] == "台湾"):
				region = "tw"
			elif(rj["data"]["region"] == "香港"):
				region = "hk"
			elif(rj["data"]["region"] == "澳门"):
				region = "mk"
			else:
				region = rj["data"]["country_id"].lower()
			break
		except:
			flag = flag + 1
			continue
	#print(region)
	print((username,clientip,region))
	items= ['username','clientip','region']
	insert_sql = geninsertsql("db_security_ssh",items)
	print(insert_sql)
	conn = pymysql.connect(host='localhost',user='root',password='123456',db='db_csmgr',charset='utf8')
	cur =  conn.cursor()
	cur.execute(insert_sql, (username,clientip,region))
	conn.commit()
# python3 sshlog.py root 134.208.1.2
if __name__ == "__main__":
	if(len(sys.argv) < 3):
		print("[*] must be 4 para")
		exit(0)
	username = sys.argv[1]
	clientip = sys.argv[2]
	sshrecord(username,clientip)
