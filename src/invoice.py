#!/usr/bin/python
# -*- coding: utf-8 -*-
# Obtenemos soporte para abrir documento para texto y para estilos

import sys
import MySQLdb
import string
from odf.opendocument import OpenDocumentText
from odf.text import H, P, Span, LineBreak, Tab, S
from odf.style import Style, ParagraphProperties, TextProperties, TableCellProperties, TableProperties, PageLayout, PageLayoutProperties, MasterPage, GraphicProperties
from odf.style import TableColumnProperties, TableRowProperties
from odf.table import Table, TableColumn, TableRow, TableCell



# Consultas a base de datos
# function que devuelve una conexion con la base de datos 
def get_connection ():
    try:
        # conectar a la base de datos 
        link = MySQLdb.connect (host = "localhost",
                                user = "prueba_login",
                                passwd = "12test34",
                                db = "prueba_login",
                                charset= "utf8")
    except MySQLdb.Error, e:
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit (1)

    return link;  
        
def do_query (query_string, link):
    cursor = link.cursor ()
    
    # ejecutar la sentencia Sql en la base de datos 
    cursor.execute (query_string)
    result = cursor.fetchall ()

    cursor.close ()
    
    return result

def cerrar_conexion (link):
   # cerrar la conexion mysql
   link.close ()

   return


def insertar_linea( n, txt ):
    i = 0
    while i < n:
        newLine = P()
        txt.text.addElement(newLine)
        i = i + 1

print "Imprimendo factura:\n"
if len(sys.argv) != 2:
    print "El comando espera recibir el id de la factura"
    exit(1)

factura_id = sys.argv[1]
print "ID Factura:" + factura_id

# Llamamos a la funcion para abrir un archivo de texto
doc = OpenDocumentText()
# Styles
s = doc.styles

# Aplicamos margenes a la pagina
pagelayout = PageLayout(name="Pagina")
pagelayout.addElement(PageLayoutProperties(marginright="3.18cm", marginleft="3.18cm", margintop="2.54cm", marginbottom="2.54cm", pagewidth="21cm", pageheight="29.7cm", printorientation="portrait"))
doc.automaticstyles.addElement(pagelayout)

masterpage = MasterPage(name="Standard", pagelayoutname=pagelayout)
doc.masterstyles.addElement(masterpage)


# Estilo de cabecera
h1style = Style(name="Cabecera", family="paragraph")
h1style.addElement(TextProperties(attributes={'fontsize':"18pt", 'fontfamily':"helvetica", 'textunderlinestyle':"solid"}))
h1style.addElement(ParagraphProperties(textalign="right", marginright="0.1cm"))
s.addElement(h1style)

# Estilo de texto

style_texto = Style(name="Texto", family="paragraph")
style_texto.addElement(TextProperties(fontfamily="Thorndale", fontcharset="utf-8"))
s.addElement(style_texto)

style_texto2 = Style(name="Texto2", family="paragraph")
style_texto2.addElement(TextProperties(fontfamily="helvetica", fontweight="bold", fontsize="12pt", fontstyle="italic"))
s.addElement(style_texto2)

style_texto3 = Style(name="Texto3", family="paragraph")
style_texto3.addElement(TextProperties(fontfamily="helvetica", fontsize="8pt"))
s.addElement(style_texto3)

style_texto4 = Style(name="Texto4", family="paragraph")
style_texto4.addElement(TextProperties(fontweight="bold", fontfamily="Times", fontsize="12pt"))
s.addElement(style_texto4)

boldstyle = Style(name="Bold", family="text")
boldprop = TextProperties(fontweight="bold", fontfamily="Bookman Light", fontsize="20", fontstyle="italic")
s.addElement(style_texto)

# Creamos los estilos que vamos a usar en las tablas
estilo_borde1 = Style(name="estilo_borde1", family="table-cell")
estilo_borde1.addElement(TableCellProperties(borderleft="0.002cm solid #000000", bordertop="0.002cm solid #000000", borderbottom="0.002cm solid #000000", padding="0.097cm"))
doc.automaticstyles.addElement(estilo_borde1)

estilo_borde2 = Style(name="estilo_borde2", family="table-cell")
estilo_borde2.addElement(TableCellProperties(borderleft="0.002cm solid #000000", borderbottom="0.002cm solid #000000", bordertop="0.002cm solid #000000", borderright="0.002cm solid #000000", padding="0.097cm"))
doc.automaticstyles.addElement(estilo_borde2)

estilo_borde3 = Style(name="estilo_borde3", family="table-cell")
estilo_borde3.addElement(TableCellProperties(borderleft="0.002cm solid #000000", borderbottom="0.002cm solid #000000", padding="0.097cm"))
doc.automaticstyles.addElement(estilo_borde3)

estilo_borde4 = Style(name="estilo_borde4", family="table-cell")
estilo_borde4.addElement(TableCellProperties(borderleft="0.002cm solid #000000", borderbottom="0.002cm solid #000000", borderright="0.002cm solid #000000", padding="0.097cm"))
doc.automaticstyles.addElement(estilo_borde4)

columna_izquierda = Style(name="Columna_izquierda", family="paragraph")
columna_izquierda.addElement(ParagraphProperties(numberlines="false", linenumber="0"))
s.addElement(columna_izquierda)

columna_derecha = Style(name="Columna_derecha", family="paragraph")
columna_derecha.addElement(ParagraphProperties(numberlines="false", linenumber="0"))
s.addElement(columna_derecha)

datos_cliente = Style(name="Datos_cliente", family="paragraph")
datos_cliente.addElement(ParagraphProperties(numberlines="false", linenumber="0"))
s.addElement(datos_cliente)

datos_concepto = Style(name="Datos_conceptos", family="paragraph")
datos_concepto.addElement(ParagraphProperties(numberlines="false", linenumber="0"))
s.addElement(datos_concepto)

datos_cantidad = Style(name="Datos_cantidad", family="table")

datos_cantidad.addElement(TableProperties(bordermodel="separating"))
s.addElement(datos_cantidad)

cabecera_concepto = Style(name="Cabecera_conceptos", family="table")
cabecera_concepto.addElement(TableProperties(align="right", bordermodel="separating"))
s.addElement(cabecera_concepto)

cabecera_total = Style(name="Cabecera_total", family="table")
cabecera_concepto.addElement(TableProperties(align="right", bordermodel="separating"))
s.addElement(cabecera_total)

datos_total = Style(name="Datos_total", family="table")
datos_concepto.addElement(TableProperties(align="right", bordermodel="separating"))
s.addElement(datos_total)

datos_precio_importe = Style(name="Datos_precio_importe", family="paragraph")
datos_precio_importe.addElement(ParagraphProperties(numberlines="false", linenumber="0"))
s.addElement(datos_precio_importe)

# Estilo para las columnas
widthshort = Style(name="widthshort", family="table-column")
widthshort.addElement(TableColumnProperties(columnwidth="1.7cm"))
s.addElement(widthshort)

column1 = Style(name="Columna 1", family="table-column")
column1.addElement(TableColumnProperties(columnwidth="1.5cm"))
doc.automaticstyles.addElement(column1)

column2 = Style(name="Columna 2", family="table-column")
column2.addElement(TableColumnProperties(columnwidth="11.5cm"))
doc.automaticstyles.addElement(column2)

column3 = Style(name="Columna 3", family="table-column")
column3.addElement(TableColumnProperties(columnwidth="2cm"))
doc.automaticstyles.addElement(column3)

column4 = Style(name="Columna 4", family="table-column")
column4.addElement(TableColumnProperties(columnwidth="2.96cm"))
s.addElement(column4)

# fontfmily cambia la fuente de la letra y textunderlinestyle subraya texto

# An automatic style
boldstyle.addElement(boldprop)
doc.automaticstyles.addElement(boldstyle)

# Text
h=H(outlinelevel=1, stylename=h1style, text="Factura")
doc.text.addElement(h)
p = P(stylename=boldstyle)
boldpart = Span(stylename=boldstyle, text=" NEXUSCOMPUTER,S.L.")

# con line-break creamos saltos de linea y con P() vacio igualmente es un salto de linea
insertar_linea(1,doc)
p.addElement(boldpart)
doc.text.addElement(p)

# ++++ TABLAS ++++

# TABLA de informacion NEXUS
table = Table()
table.addElement(TableColumn(numbercolumnsrepeated=2,stylename=widthshort))

# Abrimos fichero donde esta la informacion que vamos a insertar en la tabla
f = open('/home/dani/workspace/openinvo_aux/Programa_facturacion/archivo')

# partimos la linea segun nos encontremos punto y coma  annadimos la linea a la tabla
for line in f:
    izquierda = 0
    tr = TableRow()
    tar = line.strip().split(";")
    table.addElement(tr)
    #por cada valor de la linea partida creamos una celda e insertamos el valor 
    for val in tar: 
        if (izquierda == 0):             
            tc = TableCell()
            
            columna_izquierda.addElement(TextProperties(fontweight="bold", fontstyle="italic", fontfamily="Bookman Light", fontsize="10pt"))
            columna_izquierda.addElement(ParagraphProperties(textalign="left", marginleft="0.1cm"))
            p = P(stylename=columna_izquierda,text=val )
            tc.addElement(p)
            izquierda = 1
            tr.addElement(tc)
        else:            
            tb = TableCell()
            columna_derecha.addElement(TextProperties(fontweight="bold", fontstyle="italic", fontfamily="Bookman Light", fontsize="10pt"))
            columna_derecha.addElement(ParagraphProperties(textalign="right", marginright="0.1cm"))
            q = P(stylename=columna_derecha,text=val )
            tb.addElement(q)
            izquierda = 0
            tr.addElement(tb)
            
            doc.text.addElement(table)

# insertamos lineas en blanco
insertar_linea(1,doc)

# nos conectamos a la base de datos 
link = get_connection ();
codificacion = do_query ("SET CHARACTER SET UTF8;", link)

# obtenemos el id del cliente 
numero_fecha_factura = do_query ("SELECT DATE_FORMAT(fecha,'%d/%m/%Y'), numero from facturas where id='"+factura_id+"';", link)

for fila in numero_fecha_factura:
    digito = str(fila[1])
    fecha = str(fila[0])

    print "digito:  0" + digito
    
    if (int(digito) < 10): 
        numero = fecha[8:10] + '/' + '00' + digito
    elif (int(digito) < 100):
        numero = fecha[8:10] + '/' + '0' + digito
    else: 
        numero= fecha[8:10] + '/' + digito
    

informacion_factura = Table()
informacion_factura.addElement(TableColumn(numbercolumnsrepeated=3,stylename=widthshort))

tr = TableRow()    
informacion_factura.addElement(tr)

tb = TableCell()
r = P(stylename=style_texto, text=u'Factura Número: ') 
tb.addElement(r)
tr.addElement(tb)

tb = TableCell()
h = P(stylename=style_texto, text=numero) 
tb.addElement(h)
tr.addElement(tb)

tb = TableCell()
tr.addElement(tb)
doc.text.addElement(informacion_factura)

tr = TableRow()    
informacion_factura.addElement(tr)

tb = TableCell()
l = P(stylename=style_texto, text="Fecha: ") 
tb.addElement(l)
tr.addElement(tb)

tb = TableCell()
m = P(stylename=style_texto, text=fecha) 
tb.addElement(m)
tr.addElement(tb)

tb = TableCell()
tr.addElement(tb)
doc.text.addElement(informacion_factura)


insertar_linea(3,doc)

# TABLA de informacion del cliente 

tabla = Table()
tabla.addElement(TableColumn(numbercolumnsrepeated=2,stylename=widthshort))

# nos conectamos a la base de datos 
link = get_connection ();
codificacion = do_query ("SET CHARACTER SET UTF8;", link)

# obtenemos el id del cliente 
id_cliente = do_query ("SELECT cliente from cliente_factura where factura='"+factura_id+"'", link)

for fila in id_cliente:

# Pasamos a string el id del cliente para concatenar en la realizacion de la consulta 
    cliente = str(fila[0])

informacion_cliente = do_query ("SELECT nombre_factura, cif, direccion, CONCAT(CAST(cp AS CHAR CHARACTER SET utf8 ),'-',localidad,'(',provincia,')') from clientes where id='"+cliente+"'", link)

for fila in informacion_cliente:
    for val in fila:
        # insertamos una celda en blanco
        tr = TableRow()
        tabla.addElement(tr)
        tc = TableCell()
        tr.addElement(tc)          
        # insertamos la celda con la informacion
        tb = TableCell()
        datos_cliente.addElement(TextProperties(fontfamily="Times New Roman", fontsize="12pt"))
        datos_cliente.addElement(ParagraphProperties(textalign="left"))
        p = P(stylename=datos_cliente)        
        p.addElement (Span (text=val))            
        tb.addElement(p)
        tr.addElement(tb)
        doc.text.addElement(tabla)


insertar_linea(2,doc)

# TABLA de cabecera de conceptos

tabla2 = Table()
tabla2.addElement(TableColumn(numbercolumnsrepeated=1,stylename=column1))
tabla2.addElement(TableColumn(numbercolumnsrepeated=1,stylename=column2))
tabla2.addElement(TableColumn(numbercolumnsrepeated=2,stylename=column3))

r = open('/home/dani/workspace/openinvo_aux/Programa_facturacion/archivo4')

j = 1

for line in r:
    tr = TableRow()    
    tabla2.addElement(tr)
    tar = line.strip().split(";")
    #por cada valor de la linea partida creamos una celda e insertamos el valor 
    for val in tar:
        if (j != 4):
            tb = TableCell(stylename=estilo_borde1)
            cabecera_concepto.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt", fontweight="bold", fontstyle="italic"))
            cabecera_concepto.addElement(ParagraphProperties(textalign="center"))
            p = P(stylename=cabecera_concepto,text=val)            
            tb.addElement(p)
            tr.addElement(tb)
            j = j + 1
        else:
            tb = TableCell(stylename=estilo_borde2)
            cabecera_concepto.addElement(TextProperties(fontfamily="Times New Roman", fontsize="10pt", fontweight="bold", fontstyle="italic"))
            cabecera_concepto.addElement(ParagraphProperties(textalign="center"))
            p = P(stylename=cabecera_concepto,text=val)            
            tb.addElement(p)
            tr.addElement(tb)
# TABLA de conceptos

# nos conectamos a la base de datos 
link = get_connection ();
codificacion = do_query("SET CHARACTER SET UTF8;", link);

# obtenemos el id del cliente 
conceptos = do_query ("SELECT cantidad, concepto, precio_unidad, importe  from conceptos where factura='"+factura_id+"' order by id", link)  

total = 0

# Aplicamos cambios en los estilos de texto
datos_concepto.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))
datos_cantidad.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))
datos_precio_importe.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))

k = 0

for fila in conceptos:
    tr = TableRow()
    tabla2.addElement(tr)   
    j = 0
    total = fila[3] + total    
    # Utilizamos la variable K para construir la tabla con los bordes adecuados
    if (k == 0):        
        while j < 4: 
            if (j == 0):
                tb = TableCell(stylename=estilo_borde1)
                datos_cantidad.addElement(ParagraphProperties(textalign="left"))
                p = P(stylename=datos_cantidad,text=fila[j])
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                k = k + 1
            elif (j == 1):
                tb = TableCell(stylename=estilo_borde1)
                datos_concepto.addElement(ParagraphProperties(textalign="left"))
                p = P(stylename=datos_concepto,text=fila[j])
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                k = k + 1
            elif (j == 2):
                tb = TableCell(stylename=estilo_borde1)
                datos_precio_importe.addElement(ParagraphProperties(textalign="right"))
                p = P(stylename=datos_precio_importe,text=fila[j])
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                k = k + 1
            else:
                tb = TableCell(stylename=estilo_borde2)
                datos_precio_importe.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))
                datos_precio_importe.addElement(ParagraphProperties(textalign="right"))
                p = P(stylename=datos_precio_importe,text=fila[j])
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                k = k + 1
    else:
        while j < 4:
            if (j == 3):
                tb = TableCell(stylename=estilo_borde4)
                datos_precio_importe.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))
                datos_precio_importe.addElement(ParagraphProperties(textalign="right"))
                p = P(stylename=datos_precio_importe,text=fila[j])
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                doc.text.addElement(tabla2)
            elif (j == 0):
                tb = TableCell(stylename=estilo_borde3)
                datos_cantidad.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))
                datos_cantidad.addElement(ParagraphProperties(textalign="left"))
                p = P(stylename=datos_cantidad,text=fila[j])
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                doc.text.addElement(tabla2)
            elif (j == 1):
                tb = TableCell(stylename=estilo_borde3)
                datos_concepto.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))
                datos_concepto.addElement(ParagraphProperties(textalign="left"))
                p = P(stylename=datos_concepto)
                p.addElement (Span(text=fila[j]))
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                doc.text.addElement(tabla2)
            else:
                tb = TableCell(stylename=estilo_borde3)
                datos_precio_importe.addElement(TextProperties(fontfamily="Thorndale", fontsize="10pt"))
                datos_precio_importe.addElement(ParagraphProperties(textalign="right"))
                p = P(stylename=datos_precio_importe,text=fila[j])
                tb.addElement(p)
                tr.addElement(tb)
                doc.text.addElement(tabla2)
                j = j + 1
                doc.text.addElement(tabla2)
print total

insertar_linea(1,doc)

p = P(stylename=style_texto2, text="Total")
doc.text.addElement(p)

insertar_linea(1,doc)
# TABLA DEL TOTAL DE LA FACTURA

tabla3 = Table()
tabla3.addElement(TableColumn(numbercolumnsrepeated=6,stylename=column4))

# primera linea
tr = TableRow()    
tabla3.addElement(tr)
cabecera_total.addElement(TextProperties(fontfamily="helvetica", fontsize="8pt"))
cabecera_total.addElement(ParagraphProperties(textalign="center"))

# 6 celdas
tb = TableCell(stylename=estilo_borde1)
p = P(stylename=cabecera_total,text="Base Imponible")            
tb.addElement(p)
tr.addElement(tb)
tb = TableCell(stylename=estilo_borde1)
p = P(stylename=cabecera_total,text="% I.V.A.")            
tb.addElement(p)
tr.addElement(tb)
tb = TableCell(stylename=estilo_borde1)
p = P(stylename=cabecera_total,text="Importe I.V.A")            
tb.addElement(p)
tr.addElement(tb)
tb = TableCell(stylename=estilo_borde1)
p = P(stylename=cabecera_total,text="R.E.")            
tb.addElement(p)
tr.addElement(tb)
tb = TableCell(stylename=estilo_borde1)
p = P(stylename=cabecera_total,text="")            
tb.addElement(p)
tr.addElement(tb)
tb = TableCell(stylename=estilo_borde2)
p = P(stylename=cabecera_total,text="Total Euros")            
tb.addElement(p)
tr.addElement(tb)

# segunda linea
tr = TableRow()
tabla3.addElement(tr)
datos_total.addElement(TextProperties(fontfamily="Thorndale", fontsize="11pt"))
datos_total.addElement(ParagraphProperties(textalign="right", marginright="0.1cm"))

# 6 celdas
tb = TableCell(stylename=estilo_borde3)
p = P(stylename=datos_total,text=total)            
tb.addElement(p)
tr.addElement(tb)
tb = TableCell(stylename=estilo_borde3)
p = P(stylename=datos_total,text="16")            
tb.addElement(p)
tr.addElement(tb)
dinero = total * 16
importe = dinero / 100
tb = TableCell(stylename=estilo_borde3)
p = P(stylename=datos_total,text=importe)            
tb.addElement(p)
tr.addElement(tb)
tb = TableCell(stylename=estilo_borde3)
p = P(stylename=datos_total,text="0")            
tb.addElement(p)
tr.addElement(tb)

tb = TableCell(stylename=estilo_borde3)
p = P(stylename=datos_total,text="0")            
tb.addElement(p)
tr.addElement(tb)
total_euros = total + importe
tb = TableCell(stylename=estilo_borde4)
p = P(stylename=datos_total,text=total_euros)            
tb.addElement(p)
tr.addElement(tb)
doc.text.addElement(tabla3)

insertar_linea(2,doc)

# nos conectamos a la base de datos para insertar el total
link = get_connection ();

# Imprimimos las observaciones

observaciones = do_query ("SELECT observaciones from facturas where id='" + factura_id + "';", link)

for fila in observaciones:
    # + Para salto de linea y * para tabulacion
    enter = fila[0].strip().split("\n")
    y = P(stylename=style_texto4)
    for val in enter:        
        tabulacion = val.strip().split("\t")
        for val in tabulacion:           
            y.addElement (Span(text=val))
            doc.text.addElement(y)                    
            y.addElement (Tab())
            doc.text.addElement(y)

        y.addElement (LineBreak())
        doc.text.addElement (y)

# Insertamos el total 

insertar_total = do_query ("UPDATE facturas SET total='" + str (total_euros) + "'  WHERE id='" + factura_id + "';", link)
    
# cerramos la conexión 
cerrar_conexion (link)  


print "factura" + factura_id + ".odt"
doc.save("factura" + factura_id + ".odt")


