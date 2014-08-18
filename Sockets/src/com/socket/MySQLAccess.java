
package com.socket;

import com.socket.MySQLAccess;


import java.io.Closeable;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Date;

public class MySQLAccess {
  private Connection connect = null;
  private Statement statement = null;
  private PreparedStatement preparedStatement = null;
  private ResultSet resultSet = null;

  public void readDataBase(Connection conexion,byte[] bytes) throws Exception {
    try {
      // this will load the MySQL driver, each DB has its own driver
      //Class.forName("com.mysql.jdbc.Driver");
      // setup the connection with the DB.
      //connect = DriverManager.getConnection("jdbc:mysql://localhost/mydb?" + "user=root&password=1234");
    	
      connect = conexion;
      // statements allow to issue SQL queries to the database
      statement = connect.createStatement();
      // resultSet gets the result of the SQL query
      /*resultSet = statement
          .executeQuery("select valor from Temperatura");
      writeResultSet(resultSet);*/

      // preparedStatements can use variables and are more efficient
      for(int i = 0; i < bytes.length-1; i++)
         {    
    		 int tempe = (int)bytes[i];
    		 if(tempe < 0)
    			 tempe = 256 + tempe;
        	 
        	preparedStatement = connect
        	          .prepareStatement("insert into Temperatura (valor,Sensor_idSensor,fecha) values(?,?, NOW())");
        	      // "myuser, webpage, datum, summary, COMMENTS from FEEDBACK.COMMENTS");
        	      // parameters start with 1
        	 preparedStatement.setInt(1, tempe);
        	 preparedStatement.setInt(2,i+1);      
        	 preparedStatement.executeUpdate();
        	 System.out.println(tempe);
         }
      
      System.out.println("Correctamente ejecutado");

            
    } catch (Exception e) {
    	System.err.println("Could not close port: 10008.");
      throw e;
    } finally {
      close();
    }

  }

  private void writeMetaData(ResultSet resultSet) throws SQLException {
    // now get some metadata from the database
    System.out.println("The columns in the table are: ");
    System.out.println("Table: " + resultSet.getMetaData().getTableName(1));
    for  (int i = 1; i<= resultSet.getMetaData().getColumnCount(); i++){
      System.out.println("Column " +i  + " "+ resultSet.getMetaData().getColumnName(i));
    }
  }

  private void writeResultSet(ResultSet resultSet) throws SQLException {
    // resultSet is initialised before the first data set
    while (resultSet.next()) {
      // it is possible to get the columns via name
      // also possible to get the columns via the column number
      // which starts at 1
      // e.g., resultSet.getSTring(2);
      String value = resultSet.getString("valor");      
      System.out.println(value);
    }
  }

  // you need to close all three to make sure
  private void close() {
    //close(resultSet);
    //close(statement);
    //close(connect);
  }
  private void close(Closeable c) {
    try {
      if (c != null) {
        c.close();
      }
    } catch (Exception e) {
    // don't throw now as it might leave following closables in undefined state
    }
  }
} 
