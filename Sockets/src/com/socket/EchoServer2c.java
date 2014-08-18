package com.socket;

import java.io.BufferedWriter;
import java.net.*; 
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.io.*; 

public class EchoServer2c extends Thread
{ 
 protected static boolean serverContinue = true;
 protected Socket clientSocket;
 private static Connection connect = null;

 public static void main(String[] args) throws IOException 
   { 
	
    ServerSocket serverSocket = null; 

    try {
    	//DECLARACION PARA LA BASE 
    	Class.forName("com.mysql.jdbc.Driver");
    	connect = DriverManager
    	          .getConnection("jdbc:mysql://192.168.1.247/Monitor?" + "user=root&password=blf278");
    	
         serverSocket = new ServerSocket(10008); 
         System.out.println ("Connection Socket Created");
         try { 
              while (serverContinue)
                 {
                  serverSocket.setSoTimeout(10000);
                  System.out.println ("Waiting for Connection");
                  try {
                       new EchoServer2c (serverSocket.accept()); 
                      }
                  catch (SocketTimeoutException ste)
                      {
                       System.out.println ("Timeout Occurred");
                      }
                 }
             } 
         catch (IOException e) 
             { 
              System.err.println("Accept failed."); 
              System.exit(1); 
             } 
        } 
    catch (IOException e) 
        { 
         System.err.println("Could not listen on port: 10008."); 
         System.exit(1); 
        } catch (ClassNotFoundException e1) {
		// TODO Auto-generated catch block
		e1.printStackTrace();
	} catch (SQLException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		} 
    finally
        {
         try {
              System.out.println ("Closing Server Connection Socket");
              serverSocket.close();
              try {
      			connect.close();
      		} catch (SQLException e) {
      			// TODO Auto-generated catch block
      			e.printStackTrace();
      		}
             }
         catch (IOException e)
             { 
              System.err.println("Could not close port: 10008."); 
              System.exit(1); 
             } 
        }
   }

 private EchoServer2c (Socket clientSoc)
   {
    clientSocket = clientSoc;
    start();
   }

 
 public void grabar(String args)
 {
     FileWriter fichero = null;
     PrintWriter pw = null;
     try
     {
         fichero = new FileWriter("SocketLLegada.txt",true);
         pw = new PrintWriter(fichero);

         pw.println(args);

     } catch (Exception e) {
         e.printStackTrace();
     } finally {
        try {
        // Nuevamente aprovechamos el finally para
        // asegurarnos que se cierra el fichero.
        if (null != fichero)
           fichero.close();
        } catch (Exception e2) {
           e2.printStackTrace();
        }
     }
 }
 public void openFileToString(byte[] _bytes)
 {
     //String file_string = "";

     for(int i = 0; i < _bytes.length; i++)
     {    	 
    	 System.out.println((int)_bytes[i]);
         //file_string += (char)_bytes[i];
     }

     //return file_string;    
 }
 
 public void run()
   {
    System.out.println ("New Communication Thread Started");

    try { 
         PrintWriter out = new PrintWriter(clientSocket.getOutputStream(), 
                                      true); 
         BufferedReader in = new BufferedReader( 
                 new InputStreamReader( clientSocket.getInputStream())); 
         
         DataInputStream inn = new DataInputStream(new BufferedInputStream(clientSocket.getInputStream()));
         
                         
         MySQLAccess dao = new MySQLAccess();
         
         
         String inputLine; 
         
         while ((inputLine = in.readLine()) != null) 
             {
        	 	boolean flag = true;
        	 	
                 //Aqui todo el codigo
   	              try 
   	              {
   	            	byte[] bytes = new byte[17];
   	            	inn.read(bytes);
   	             /*for(int i = 2; i < bytes.length; i++)
	   	          {    
	   	     		 int tempe = (int)bytes[i];
	   	     		 if(tempe != 0)
	   	     			  flag = true;	   	         	
	   	          }*/
   	        //Grabando en la base de datos MySQL
   	             if(flag)   	            	
   	            	dao.readDataBase(connect,bytes);  				
   	      		} catch (Exception e) {
   	      			// TODO Auto-generated catch block
   	      			e.printStackTrace();
   	      		} 
   	           } 

         out.close(); 
         in.close(); 
         clientSocket.close();
        
        } 
    catch (IOException e) 
        { 
         System.err.println("Problem with Communication Server");
         System.exit(1); 
        } 
    }
} 
